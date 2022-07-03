import { createApp, h } from 'vue';
import Modules from "./Modules";

createApp({
  render: () => h(Modules)
}).mount('#app');
