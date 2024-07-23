<template>
    <div class="chart-container">
        <component :is="chartComponent" :data="chartData" :options="chartOptions"></component>
    </div>
</template>

<script>
import { Line, Bar, Pie, Doughnut } from 'vue-chartjs';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

export default {
    components: {
        LineChart: Line,
        BarChart: Bar,
        PieChart: Pie,
        Doughnut: Doughnut
    },
    props: {
        chartData: {
            type: Object,
            required: true
        },
        chartOptions: {
            type: Object,
            required: false,
            default: () => ({})
        },
        chartType: {
            type: String,
            required: true
        }
    },
    computed: {
        chartComponent() {
            switch (this.chartType) {
                case 'line':
                    return 'LineChart';
                case 'bar':
                    return 'BarChart';
                case 'pie':
                    return 'PieChart';
                case 'doughnut':
                    return 'Doughnut';
                default:
                    return 'LineChart';
            }
        }
    }
};
</script>

<style scoped>
.chart-container {
    position: relative;
    height: 200px;
    width: 100%;
}
</style>