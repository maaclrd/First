import axios from 'axios';
import { defineStore } from 'pinia';
import { normalizeErrorBag } from '@/utils/validationMessages';

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
        minPrice: '',
        maxPrice: '',
        minStock: '',
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
            this.message = '';

            try {
                const { data } = await axios.get('/api/products', {
                    params: {
                        page,
                        search: this.search || undefined,
                        min_price: this.minPrice !== '' ? this.minPrice : undefined,
                        max_price: this.maxPrice !== '' ? this.maxPrice : undefined,
                        min_stock: this.minStock !== '' ? this.minStock : undefined,
                        per_page: this.pagination.per_page,
                    },
                });

                this.items = data.data.items.data ?? data.data.items;
                this.pagination = data.data.pagination;
                this.message = data.message;
            } catch (error) {
                const statusCode = error.response?.status;

                if (statusCode === 401) {
                    this.items = [];
                    this.pagination = {
                        current_page: 1,
                        last_page: 1,
                        per_page: 10,
                        total: 0,
                    };
                    this.errors = {
                        request: ['Sessão expirada. Por favor, faça login novamente.'],
                    };
                    return;
                }

                if (!statusCode || statusCode >= 500) {
                    this.errors = {
                        request: ['Não foi possível carregar os produtos.'],
                    };
                }
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
                const bag = error.response?.data?.errors;
                if (bag && typeof bag === 'object' && !Array.isArray(bag)) {
                    this.errors = normalizeErrorBag(bag);
                } else {
                    this.errors = {
                        request: ['Não foi possível criar o produto.'],
                    };
                }
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
                const bag = error.response?.data?.errors;
                if (bag && typeof bag === 'object' && !Array.isArray(bag)) {
                    this.errors = normalizeErrorBag(bag);
                } else {
                    this.errors = {
                        request: ['Não foi possível atualizar o produto.'],
                    };
                }
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
                const bag = error.response?.data?.errors;
                if (bag && typeof bag === 'object' && !Array.isArray(bag)) {
                    this.errors = normalizeErrorBag(bag);
                } else {
                    this.errors = {
                        request: ['Não foi possível remover o produto.'],
                    };
                }
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

        clearProductFieldError(field) {
            if (!this.errors?.[field]) {
                return;
            }
            const next = { ...this.errors };
            delete next[field];
            this.errors = Object.keys(next).length ? next : null;
        },

        clearProductFormErrors() {
            if (!this.errors) {
                return;
            }
            const keys = ['name', 'description', 'price', 'stock'];
            const next = { ...this.errors };
            keys.forEach((k) => delete next[k]);
            this.errors = Object.keys(next).length ? next : null;
        },
    },
});
