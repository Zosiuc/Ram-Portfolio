<template>
  <section class="analytics">
    <h1 class="">Analytics</h1>
    <div class="canvas">
      <LineChart  :data="chartData" />
    </div>

  </section>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { api } from '../lib/api';
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement, type ChartData } from 'chart.js';
import { Line } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement);

const chartData = ref<ChartData<'line'>>({
  labels: [],
  datasets: [
    {
      label: 'Visits',
      data: [],
      borderColor: 'blue'
    }
  ]
});

const LineChart = Line;

onMounted(async () => {
  const { data } = await api.get('/analytics/summary');
  chartData.value = {
    labels: data.last30d.map((d: any) => d.day),
    datasets: [
      {
        label: 'Visits',
        data: data.last30d.map((d: any) => d.visits),
        borderColor: 'blue'
      }
    ]
  };
});

</script>
<style  scoped>
.analytics {
  display: flex;
  flex-direction: column;
  align-items: center;
  max-height: 500px;
  gap:2rem;

  h1 {
    font-size: x-large;
  }
}

.canvas{


  canvas{
    background-color: var(--color-background-soft-blur);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: .2rem;
    width: 100%;
    height: 100%;
    border-radius: 12px;
  }


}
</style>
