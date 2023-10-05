import {defineStore, storeToRefs} from "pinia";

export const getUserStore = defineStore('user', {
  state: () => ({
    logged_in: false,
    name: '',
    email: '',
    picture_url: '',
  }),
});

export const reactiveUserStore = storeToRefs(getUserStore());