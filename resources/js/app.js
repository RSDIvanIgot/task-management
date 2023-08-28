
require('./bootstrap');

window.Vue = require('vue').default;

Vue.component("kanban-board", require("./components/KanbanBoard.vue").default);
Vue.component("task-card", require("./components/TaskCard.vue").default);

import Toasted from 'vue-toasted';
import Vue from 'vue'
Vue.use(Toasted, {
    duration: 1500,
    position: 'top-right', // ['top-right', 'top-center', 'top-left', 'bottom-right', 'bottom-center', 'bottom-left']
    theme: 'toasted-primary', // ['toasted-primary', 'outline', 'bubble']
    iconPack: 'material' // ['material', 'fontawesome', 'mdi', 'custom-class', 'callback']
})

const app = new Vue({
    el: '#app',
});
