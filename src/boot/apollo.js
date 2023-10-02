import { ApolloClient} from '@apollo/client/core'
import { createApolloProvider } from '@vue/apollo-option'
import { boot } from 'quasar/wrappers'
import { getClientOptions } from 'src/apollo'

export default boot(({ app }) => {
  const options = getClientOptions();
  const apolloClient = new ApolloClient(options);

  const apolloProvider = createApolloProvider({
    defaultClient: apolloClient,
  });

  app.use(apolloProvider);

  app.provide('$apollo', apolloClient);
})
