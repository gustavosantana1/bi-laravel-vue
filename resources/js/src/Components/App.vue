<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#"><b class="p-3">Dashboard - BI</b></a>
        </nav>

        <div class="d-flex">
            <div class="bg-light border" style="width: 250px;">
                <div class="p-3">
                    <h5>Filters</h5>
                    <div class="mb-3">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select id="statusFilter" class="form-select" v-model="filters.status">
                            <option value="">All</option>
                            <option value="A">Approved</option>
                            <option value="C">Canceled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="productNameFilter" class="form-label">Product Name</label>
                        <input type="text" id="productNameFilter" class="form-control" v-model="filters.product_name">
                    </div>
                    <div class="mb-3">
                        <label for="paymentFilter" class="form-label">Payment Method</label>
                        <select id="paymentFilter" class="form-select" v-model="filters.payment_method">
                            <option value="">All</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="money">Money</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amountFilter" class="form-label">Min Amount</label>
                        <input type="number" id="amountFilter" class="form-control" v-model="filters.min_amount">
                    </div>
                    <div class="mb-3">
                        <label for="descriptionFilter" class="form-label">Description</label>
                        <input type="text" id="descriptionFilter" class="form-control" v-model="filters.description">
                    </div>
                    <div class="mb-3">
                        <label for="dateFilter" class="form-label">Purchase Date</label>
                        <input type="date" id="dateFilter" class="form-control" v-model="filters.purchase_date">
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 col-lg-4" v-for="(chart, index) in charts" :key="index">
                        <chart-component :chart-data="chart.data" :chart-options="chart.options" :chart-type="chart.type"></chart-component>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <details-component :items="filteredItems"></details-component>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import ChartComponent from './ChartComponent.vue';
import DetailsComponent from './DetailsComponent.vue';

export default {
    components: {
        ChartComponent,
        DetailsComponent
    },
    data() {
        return {
            items: [],
            filters: {
                status: '',
                payment_method: '',
                min_amount: '',
                purchase_date: '',
                product_name: '',
                description: ''
            },
            charts: []
        };
    },
    computed: {
        filteredItems() {
            return this.items.filter(item => {
                return (
                    (!this.filters.status || item.status === this.filters.status) &&
                    (!this.filters.payment_method || item.payment_method === this.filters.payment_method) &&
                    (!this.filters.min_amount || item.amount >= this.filters.min_amount) &&
                    (!this.filters.purchase_date || item.purchase_date === this.filters.purchase_date) &&
                    (!this.filters.product_name || item.product_name.toLowerCase().includes(this.filters.product_name.toLowerCase())) &&
                    (!this.filters.description || item.description.toLowerCase().includes(this.filters.description.toLowerCase()))
                );
            });
        }
    },
    watch: {
        filters: {
            deep: true,
            handler() {
                this.fetchData();
            }
        }
    },
    mounted() {
        this.fetchData();
    },
    methods: {
        fetchData() {
            axios.get('/api/sales_data')
                .then(response => {
                    this.items = response.data;
                    this.updateCharts();
                })
                .catch(error => {
                    console.error(error);
                });
        },
        updateCharts() {
            this.charts = [
                {
                    data: this.generateProductSalesData(this.filteredItems),
                    type: 'line'
                },
                {
                    data: this.generateTotalSalesData(this.filteredItems),
                    type: 'line'
                },
                {
                    data: this.generatePaymentMethodData(this.filteredItems),
                    type: 'bar'
                },
                {
                    data: this.generateTopSellingProductsData(this.filteredItems),
                    type: 'line'
                },
                {
                    data: this.generateAverageOrderValueData(this.filteredItems),
                    type: 'line'
                },
                {
                    data: this.generateSalesByStatusData(this.filteredItems),
                    type: 'doughnut'
                }
            ];
        },
        generateProductSalesData(items) {
            const productSales = items.reduce((acc, item) => {
                acc[item.product_name] = (acc[item.product_name] || 0) + item.qty;
                return acc;
            }, {});

            return {
                labels: Object.keys(productSales),
                datasets: [
                    {
                        label: 'Quantidade Vendida por Produto',
                        backgroundColor: '#f87979',
                        data: Object.values(productSales)
                    }
                ]
            };
        },
        generateTotalSalesData(items) {
            const salesByDate = items.reduce((acc, item) => {
                const dateKey = this.filters.purchase_date ? item.purchase_date : item.purchase_date.slice(0, 4);
                acc[dateKey] = (acc[dateKey] || 0) + parseFloat(item.amount);
                return acc;
            }, {});

            return {
                labels: Object.keys(salesByDate),
                datasets: [
                    {
                        label: 'Vendas Totais (Valor)',
                        backgroundColor: '#36a2eb',
                        data: Object.values(salesByDate)
                    }
                ]
            };
        },
        generatePaymentMethodData(items) {
            const paymentMethods = items.reduce((acc, item) => {
                acc[item.payment_method] = (acc[item.payment_method] || 0) + 1;
                return acc;
            }, {});

            return {
                labels: Object.keys(paymentMethods).map(method => {
                    switch (method) {
                        case 'credit_card': return 'Cartão de Crédito';
                        case 'bank_transfer': return 'Transferência Bancária';
                        case 'money': return 'Dinheiro';
                        case 'paypal': return 'PayPal';
                        default: return method;
                    }
                }),
                datasets: [
                    {
                        label: 'Métodos de Pagamento',
                        backgroundColor: ['#ff6384', '#36a2eb', '#cc65fe', '#ffce56'],
                        data: Object.values(paymentMethods)
                    }
                ]
            };
        },
        generateTopSellingProductsData(items) {
            const productSales = items.reduce((acc, item) => {
                acc[item.product_name] = (acc[item.product_name] || 0) + item.qty;
                return acc;
            }, {});

            const sortedProducts = Object.entries(productSales).sort((a, b) => b[1] - a[1]).slice(0, 5);

            return {
                labels: sortedProducts.map(entry => entry[0]),
                datasets: [
                    {
                        label: 'Top Produtos Vendidos',
                        backgroundColor: '#ffce56',
                        data: sortedProducts.map(entry => entry[1])
                    }
                ]
            };
        },
        generateAverageOrderValueData(items) {
            const totalValue = items.reduce((acc, item) => acc + parseFloat(item.amount), 0);
            const averageValue = totalValue / items.length;

            return {
                labels: ['Valor Médio dos Pedidos'],
                datasets: [
                    {
                        label: 'Valor Médio dos Pedidos',
                        backgroundColor: '#ff6384',
                        data: [averageValue]
                    }
                ]
            };
        },
        generateSalesByStatusData(items) {
            const salesByStatus = items.reduce((acc, item) => {
                const statusLabel = item.status === 'C' ? 'Canceled' : (item.status === 'A' ? 'Approved' : item.status);
                acc[statusLabel] = (acc[statusLabel] || 0) + parseFloat(item.amount);
                return acc;
            }, {});

            return {
                labels: Object.keys(salesByStatus),
                datasets: [
                    {
                        label: 'Vendas por Status',
                        backgroundColor: ['#ff6384', '#36a2eb'],
                        data: Object.values(salesByStatus)
                    }
                ]
            };
        }
    }
};
</script>

<style scoped>
.chart-container {
    margin-bottom: 20px;
}

.chart-component {
    margin-bottom: 20px;
}
</style>