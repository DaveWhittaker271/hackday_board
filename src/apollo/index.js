import { createHttpLink, InMemoryCache, ApolloLink, from } from '@apollo/client/core'

export function getClientOptions() {
  // Automatic middleware to include the auth token on out goign requests if present
  const authenticationMiddleware = new ApolloLink((operation, forward) => {
    const token = localStorage.getItem('authToken');

    if (token) {
      operation.setContext(({headers = {}}) => ({
          headers: {
              ...headers,
              'HTTP_AUTHORIZATION': 'Bearer ' + token,
          }
      }));
    }

    return forward(operation);
  });

  // Assign middlewares, options and endpoint URL
  return Object.assign(
    {
      link: from([
        authenticationMiddleware,
        createHttpLink({
          uri: '/webapi/graphql/endpoint/',
        }),
      ]),
      cache: new InMemoryCache(),
    },
  )
}
