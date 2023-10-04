import {defineStore} from "pinia";

export const getUserStore = defineStore('user', {
  state: () => ({
    name: '',
    email: '',
    picture_url: '',
  }),
});