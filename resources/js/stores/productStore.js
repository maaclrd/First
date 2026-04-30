import axios from 'axios';
import { defineStore } from 'pinia';

export const useProductStore = defineStore('products', {
    state: () => ({
        items: [],
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 10,
            total: 0,
        },
        search: '',
        loading: false,
        submitting: false,
        message: '',
        errors: null,
        selectedProduct: null,
    }),
    actions: {
        async fetchProducts(page = 1) {
            this.loading = true;
            this.errors = null;

            try {
                const { data } = await axios.get('/api/products', {
                    params: {
                        page,
                        search: this.search || undefined,
                        per_page: this.pagination.per_page,
                    },
                });

                this.items = data.data.items.data ?? data.data.items;
                this.pagination = data.data.pagination;
                this.message = data.message;
            } catch (error) {
                this.errors = error.response?.data?.errors ?? {
                    request: ['Não foi possível carregar os produtos.'],
                };
            } finally {
                this.loading = false;
            }
        },

        async createProduct(payload) {
            this.submitting = true;
            this.errors = null;

            try {
                const { data } = await axios.post('/api/products', payload);
                this.message = data.message;
                await this.fetchProducts(1);
                return true;
            } catch (error) {
                this.errors = error.response?.data?.errors ?? {
                    request: ['Não foi possível criar o produto.'],
                };
                return false;
            } finally {
                this.submitting = false;
            }
        },

        async updateProduct(id, payload) {
            this.submitting = true;
            this.errors = null;

            try {
                const { data } = await axios.put(`/api/products/${id}`, payload);
                this.message = data.message;
                await this.fetchProducts(this.pagination.current_page);
                return true;
            } catch (error) {
                this.errors = error.response?.data?.errors ?? {
                    request: ['Não foi possível atualizar o produto.'],
                };
                return false;
            } finally {
                this.submitting = false;
            }
        },

        async deleteProduct(id) {
            this.submitting = true;
            this.errors = null;

            try {
                const { data } = await axios.delete(`/api/products/${id}`);
                this.message = data.message;
                await this.fetchProducts(this.pagination.current_page);
                return true;
            } catch (error) {
                this.errors = error.response?.data?.errors ?? {
                    request: ['Não foi possível remover o produto.'],
                };
                return false;
            } finally {
                this.submitting = false;
            }
        },

        setSearch(value) {
            this.search = value;
        },

        clearFeedback() {
            this.message = '';
            this.errors = null;
        },
    },
});
