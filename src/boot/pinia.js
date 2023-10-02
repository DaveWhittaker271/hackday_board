import { boot } from 'quasar/wrappers'
import { createPinia, defineStore } from 'pinia'

export default boot(({ app }) => {
  app.use(createPinia())
})
