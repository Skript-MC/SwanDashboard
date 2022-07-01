import { createApp, h } from 'vue';
import Editor from "./Editor";
import './diff2html.css';

createApp({
  render: () => h(Editor)
}).mount('#app');
