<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Analytics</h1>
    <LineChart :data="chartData" />
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { api } from '../lib/api';
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement } from 'chart.js';
import { Line } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement);

const chartData = ref({ labels: [], datasets: [] });
const LineChart = Line;

onMounted(async () => {
  const { data } = await api.get('/analytics/summary');
  chartData.value = {
    labels: data.last30d.map(d => d.day),
    datasets: [{ label: 'Visits', data: data.last30d.map(d => d.visits), borderColor: 'blue' }]
  };
});
</script>
