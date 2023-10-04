<template>
  <q-layout view="hHh lpr fFf">
    <q-header class="header-bar">
      <q-toolbar class="q-px-sm items-stretch">
        <q-toolbar-title class="row items-center">
          <q-icon name="bi-lightbulb" class="q-pa-sm"/>
          <span class="logo vertical-middle">
            Hackday Ideas
          </span>
        </q-toolbar-title>

        <LoginArea />
      </q-toolbar>
    </q-header>

    <q-page-container>
      <q-page class="flex q-pa-lg justify-center full-width">
        <router-view/>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent } from 'vue'

import LoginArea from 'layouts/components/LoginArea.vue';

export default defineComponent({
  name: 'MainLayout',
  components: {
    LoginArea
  },
})
</script>
