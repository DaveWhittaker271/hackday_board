<template>
  <q-btn-dropdown :unelevated="true" class="full-height">
    <template v-slot:label>
      <q-avatar size="35px" class="q-mr-sm">
        <img :src="userStore.picture_url">
      </q-avatar>
      <span>
        {{ userStore.name }}
      </span>
    </template>

    <div class="column no-wrap q-pa-md items-center">
      <q-avatar size="72px">
        <img :src="userStore.picture_url">
      </q-avatar>

      <div class="text-subtitle1 q-mt-md q-mb-xs items-center">
        <div>{{ userStore.name }}</div>
      </div>

      <q-btn
          color="primary"
          label="Logout"
          push
          size="sm"
          v-close-popup
          @click="logout"
      />
    </div>
  </q-btn-dropdown>
</template>

<script>
import { defineComponent } from 'vue'

import { getUserStore } from 'stores/user.js';

export default defineComponent({
  name: 'LoggedInMenu',
  data() {
    return {
      userStore: getUserStore(),
    }
  },
  methods: {
    logout() {
      window.google.accounts.id.revoke(this.userStore.email, () => {
        localStorage.removeItem('authToken');
        this.$emit('logged-out');
        console.log('Logged Out');
      });
    }
  }
})
</script>
