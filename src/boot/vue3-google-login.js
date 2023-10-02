import { boot } from 'quasar/wrappers'
import GoogleLogin from 'vue3-google-login'

export default boot(({ app }) => {
  app.use(GoogleLogin, {
    clientId: process.env.GOOGLE_CLIENT_ID
  })
})
