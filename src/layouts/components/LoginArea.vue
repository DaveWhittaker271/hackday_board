<template>
  <div class="q-gutter-none row items-center q-mr-lg">
    <template v-if="!signedIn">
      <div v-show="!verifyingLogin" ref="googleLoginButton" />

      <template v-if="verifyingLogin">
        Signing in...
      </template>
    </template>

    <LoggedInMenu v-else @logged-out="logout" />
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import { googleSdkLoaded, decodeCredential } from 'vue3-google-login';

import LoggedInMenu from 'layouts/components/LoggedInMenu.vue';

import { getUserStore } from 'stores/user.js';

import loginMutation from 'mutation/login.graphql';

export default defineComponent({
  name: 'LoginArea',
  components: {
    LoggedInMenu,
  },
  data() {
    return {
      signedIn: false,
      verifyingLogin: false,
      userStore: getUserStore(),
    }
  },
  mounted() {
    const authToken = this.checkForAuthToken();

    let userDetails = null;
    try {
      userDetails = decodeCredential(authToken);
    } catch {
      console.log('Invalid JWT token');
    }

    if (userDetails) {
      this.verifyingLogin = true;

      this.attemptLogin(authToken);
    } else {
      googleSdkLoaded(google => {
        google.accounts.id.initialize({
          client_id: process.env.GOOGLE_CLIENT_ID,
          callback: this.handleCredentialResponse,
          auto_select: true
        });

        this.renderLoginButton();
      });
    }
  },
  methods: {
    renderLoginButton() {
      google.accounts.id.renderButton(this.$refs.googleLoginButton, {
        text: 'signin_with',
        size: 'large',
        width: '250',
        theme: 'outline',
        logo_alignment: 'left',
      });
    },
    checkForAuthToken() {
      const authToken = localStorage.getItem('authToken');

      if (!authToken) {
        return false;
      }

      return authToken;
    },
    handleCredentialResponse(credentialData) {
      this.verifyingLogin = true;

      console.log('Received login response from Google API');

      this.attemptLogin(credentialData.credential);
    },
    attemptLogin(token) {
      this.$apollo.mutate({
        mutation: loginMutation,
        fetchPolicy: 'network-only',
        variables: {
          'jwt_token': token
        }
      }).then(result => {
        this.verifyingLogin = false;

        if (result.data.login) {
          localStorage.setItem('authToken', token);
          this.loggedIn(result.data.login, decodeCredential(token));
        }
      }).catch(async error => {
        console.error('Unable to sign-in');
        this.verifyingLogin = false;
        localStorage.removeItem('authToken');

        await this.$nextTick();

        this.renderLoginButton()
      });
    },
    loggedIn(id, userDetails) {
      this.signedIn = true;

      this.userStore.$patch({
        'logged_in': true,
        'id': id,
        'name': userDetails.name,
        'picture_url': userDetails.picture,
        'email': userDetails.email
      });
    },
    async logout() {
      this.signedIn = false;
      localStorage.removeItem('authToken');
      this.userStore.$patch({
        'logged_in': false,
        'id': null,
        'name': '',
        'picture_url': '',
        'email': ''
      });

      await this.$nextTick();

      this.renderLoginButton()
    }
  }
})
</script>
