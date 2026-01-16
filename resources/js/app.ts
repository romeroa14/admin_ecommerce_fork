import './bootstrap';
import { createApp } from 'vue';

// Import components
import ExampleComponent from './components/ExampleComponent.vue';

const app = createApp({});

// Register components
app.component('example-component', ExampleComponent);

// Mount the app if the element exists
if (document.getElementById('app')) {
    app.mount('#app');
}
