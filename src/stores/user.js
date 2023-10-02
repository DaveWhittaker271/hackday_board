import {defineStore} from "pinia";

export const getUserStore = defineStore('user', {
  state: () => ({
    name: '',
    picture_url: '',
  }),
});