import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        identity: 0,
        employeeid: 0,
        token: '',
        restaurantName: 'null',
        restaurantIcon: 'https://secure.gravatar.com/avatar/'
    },
    mutations: {
        setRestaurantName(state, value) {
            state.restaurantName = value;
        },
        setRestaurantIcon(state, value) {
            state.restaurantIcon = value;
        },
        setEmployeeid(state, value){
            state.employeeid = value;
        },
        setToken(state, value){
            state.token = value;
        },
        setIdentity(state, value) {
            state.identity = value;
        }
    },
    actions: {

    }
})