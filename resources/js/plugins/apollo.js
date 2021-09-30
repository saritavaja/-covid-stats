import Vue from 'vue';
import VueApollo from 'vue-apollo';

Vue.use(VueApollo);

import {ApolloClient} from 'apollo-client';
import {createHttpLink} from 'apollo-link-http';
import {InMemoryCache} from 'apollo-cache-inmemory';

const httpLink = createHttpLink({
    uri: (process.env.NODE_ENV === 'development' ? '' : process.env.MIX_PRODUCTION_URL) + '/api/graphql',
});

const cache = new InMemoryCache();

// Create the apollo client
const apolloClient = new ApolloClient({
    link: httpLink,
    cache,
});

const apolloProvider = new VueApollo({
    defaultClient: apolloClient,
});

export default apolloProvider;
