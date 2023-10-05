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
    const userDetails = this.checkForAuthToken();

    if (userDetails) {
      this.loggedIn(userDetails);
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

      try {
        return decodeCredential(authToken);
      } catch {
        console.log('Invalid JWT token');
        return false;
      }
    },
    handleCredentialResponse(credentialData) {
      this.verifyingLogin = true;

      console.log('Received login response from Google API');

      this.$apollo.mutate({
        mutation: loginMutation,
        fetchPolicy: 'network-only',
        variables: {
          'jwt_token': credentialData.credential
        }
      }).then(result => {
        this.verifyingLogin = false;

        if (result.data.login) {
          localStorage.setItem('authToken', credentialData.credential);
          this.loggedIn(decodeCredential(credentialData.credential));
        }
      });
    },
    loggedIn(userDetails) {
      this.signedIn = true;

      this.userStore.$patch({
        'name': userDetails.name,
        'picture_url': userDetails.picture,
        'email': userDetails.email
      });
    },
    async logout() {
      this.signedIn = false;
      await this.$nextTick();
      this.renderLoginButton()
    }
  }
})
</script>
