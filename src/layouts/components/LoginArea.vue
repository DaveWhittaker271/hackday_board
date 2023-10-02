<template>
  <div class="q-gutter-none column items-center q-mr-lg">
    <template v-if="!signedIn">
      <div v-show="!verifyingLogin" ref="googleLoginButton" />

      <template v-if="verifyingLogin">
        Signing in...
      </template>
    </template>
    <div v-else class="row items-center">
      <img :src="userStore.picture_url" height="30" class="q-mr-sm" /> {{ userStore.name }}
    </div>
  </div>
</template>

<script setup>
</script>

<script>
import { defineComponent } from 'vue'
import { googleSdkLoaded, decodeCredential } from 'vue3-google-login';
import { getUserStore } from 'stores/user.js';

import loginMutation from 'mutation/login.graphql';

export default defineComponent({
  name: 'LoginArea',
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
      window.google.accounts.id.initialize({
        client_id: process.env.GOOGLE_CLIENT_ID,
        callback: this.handleCredentialResponse,
        auto_select: true
      });

      googleSdkLoaded(google => {
        window.google.accounts.id.renderButton(this.$refs.googleLoginButton, {
          text: 'signin_with',
          size: 'large',
          width: '250',
          theme: 'outline',
          logo_alignment: 'left',
        });
      });
    }
  },
  methods: {
    checkForAuthToken() {
      const authToken = localStorage.getItem('authToken');

      if (!authToken) {
        return false;
      }

      return decodeCredential(authToken);
    },
    handleCredentialResponse(credentailData) {
      this.verifyingLogin = true;

      this.$apollo.mutate({
        mutation: loginMutation,
        fetchPolicy: 'network-only',
        variables: {
          'jwt_token': credentailData.credential
        }
      }).then(result => {
        this.verifyingLogin = false;

        if (result.data.login) {
          localStorage.setItem('authToken', credentailData.credential);
          this.loggedIn(decodeCredential(credentailData.credential));
        }
      });
    },
    loggedIn(userDetails) {
      this.signedIn = true;

      this.userStore.$patch({
        'name': userDetails.name,
        'picture_url': userDetails.picture
      });
    }
  }
})
</script>
