import { Chart, BarController, CategoryScale, LinearScale, BarElement, LineElement } from 'chart.js';
Chart.register(BarController, CategoryScale, LinearScale, BarElement, LineElement);
window.Chart = Chart;
