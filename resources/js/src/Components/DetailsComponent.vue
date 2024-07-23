<template>
    <div>
        <h5>Orders</h5>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Amount</th>
                    <th>Purchase Date</th>
                    <th>Payment Method</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in paginatedItems" :key="item.id">
                    <td>{{ item.id }}</td>
                    <td>{{ item.product_name }}</td>
                    <td>{{ item.amount }}</td>
                    <td>{{ item.purchase_date }}</td>
                    <td>{{ formatPaymentMethod(item.payment_method) }}</td>
                    <td>{{ item.description }}</td>
                    <td>
                        <button class="btn btn-info btn-sm" @click="fetchOrderDetails(item.id)">Details</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <nav>
            <ul class="pagination">
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <a class="page-link" href="#" @click.prevent="changePage(1)">First</a>
                </li>
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">Previous</a>
                </li>

                <li v-if="visiblePages[0] > 1" class="page-item">
                    <a class="page-link" href="#" @click.prevent="changePage(1)">1</a>
                </li>
                <li v-if="visiblePages[0] > 2" class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
                <li v-for="page in visiblePages" :key="page" class="page-item"
                    :class="{ active: currentPage === page }">
                    <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                </li>
                <li v-if="visiblePages[visiblePages.length - 1] < totalPages - 1" class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
                <li v-if="visiblePages[visiblePages.length - 1] < totalPages" class="page-item">
                    <a class="page-link" href="#" @click.prevent="changePage(totalPages)">{{ totalPages }}</a>
                </li>

                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                    <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">Next</a>
                </li>
                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                    <a class="page-link" href="#" @click.prevent="changePage(totalPages)">Last</a>
                </li>
            </ul>
        </nav>
        
        <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="orderDetails">
                            <p><strong>Product:</strong> {{ orderDetails.product_name }}</p>
                            <p><strong>Amount:</strong> {{ orderDetails.amount }}</p>
                            <p><strong>Quantity:</strong> {{ orderDetails.qty }}</p>
                            <p><strong>Purchase Date:</strong> {{ orderDetails.purchase_date }}</p>
                            <p><strong>Payment Method:</strong> {{ formatPaymentMethod(orderDetails.payment_method) }}
                            </p>
                            <p><strong>Description:</strong> {{ orderDetails.description }}</p>
                            <p><strong>Status:</strong> {{ formatStatus(orderDetails.status) }}</p>
                        </div>
                        <div v-else>
                            <p>Loading...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        items: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            orderDetails: null,
            currentPage: 1,
            itemsPerPage: 20
        };
    },
    computed: {
        totalPages() {
            return Math.ceil(this.items.length / this.itemsPerPage);
        },
        paginatedItems() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.items.slice(start, end);
        },
        visiblePages() {
            let startPage = Math.max(1, this.currentPage - 4);
            let endPage = Math.min(this.totalPages, this.currentPage + 4);

            if (this.currentPage <= 5) {
                endPage = Math.min(10, this.totalPages);
            }
            if (this.currentPage >= this.totalPages - 4) {
                startPage = Math.max(this.totalPages - 9, 1);
            }

            const pages = [];
            for (let i = startPage; i <= endPage; i++) {
                pages.push(i);
            }
            return pages;
        }
    },
    methods: {
        fetchOrderDetails(id) {
            this.orderDetails = null;
            axios.get(`/api/sales_data/${id}`)
                .then(response => {
                    this.orderDetails = response.data;
                    const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
                    modal.show();
                })
                .catch(error => {
                    if (error.response.status === 404) {
                        alert('Order not found!');
                    } else {
                        alert('An error has occurred');
                    }
                });
        },
        changePage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
            }
        },
        formatPaymentMethod(paymentMethod) {
            switch (paymentMethod) {
                case 'credit_card': return 'Credit Card';
                case 'bank_transfer': return 'Bank Transfer';
                case 'money': return 'Money';
                case 'paypal': return 'PayPal';
                default: return paymentMethod;
            }
        },
        formatStatus(status) {
            switch (status) {
                case 'A': return 'Approved';
                case 'C': return 'Canceled';
                default: return status;
            }
        },
        changePage(page) {
            if (page > 0 && page <= this.totalPages) {
                this.currentPage = page;
            }
        }
    }
};
</script>

<style scoped>

.pagination {
    justify-content: center;
}

.page-item.disabled .page-link {
    cursor: not-allowed;
}

.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}
</style>
