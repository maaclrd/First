<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useProductStore } from '@/stores/productStore';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, reactive, ref } from 'vue';

const store = useProductStore();

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);

const createForm = reactive({
    name: '',
    description: '',
    price: '',
    stock: '',
});

const editForm = reactive({
    id: null,
    name: '',
    description: '',
    price: '',
    stock: '',
});

const flattenedErrors = computed(() =>
    store.errors ? Object.values(store.errors).flat() : [],
);

const openCreateModal = () => {
    store.clearFeedback();
    createForm.name = '';
    createForm.description = '';
    createForm.price = '';
    createForm.stock = '';
    showCreateModal.value = true;
};

const openViewModal = (product) => {
    store.selectedProduct = product;
    showViewModal.value = true;
};

const openEditModal = (product) => {
    store.clearFeedback();
    editForm.id = product.id;
    editForm.name = product.name;
    editForm.description = product.description ?? '';
    editForm.price = product.price;
    editForm.stock = product.stock;
    showEditModal.value = true;
};

const createProduct = async () => {
    const success = await store.createProduct({
        name: createForm.name,
        description: createForm.description || null,
        price: Number(createForm.price),
        stock: Number(createForm.stock),
    });

    if (success) {
        showCreateModal.value = false;
    }
};

const updateProduct = async () => {
    const success = await store.updateProduct(editForm.id, {
        name: editForm.name,
        description: editForm.description || null,
        price: Number(editForm.price),
        stock: Number(editForm.stock),
    });

    if (success) {
        showEditModal.value = false;
    }
};

const removeProduct = async (id) => {
    if (window.confirm('Deseja realmente remover este produto?')) {
        await store.deleteProduct(id);
    }
};

const applySearch = async () => {
    await store.fetchProducts(1);
};

onMounted(async () => {
    await store.fetchProducts();
});
</script>

<template>
    <Head title="Produtos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-[#0042b1]">
                Gestão de Produtos
            </h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <section class="rounded-xl border border-blue-100 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-[#0042b1]">Produtos</h3>
                            <p class="text-sm text-slate-600">Consulta, cadastro e atualização de produtos.</p>
                        </div>
                        <button
                            type="button"
                            class="rounded-lg bg-[#0042b1] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#003894]"
                            @click="openCreateModal"
                        >
                            Novo Produto
                        </button>
                    </div>

                    <div class="mt-6 grid gap-3 md:grid-cols-[1fr_auto]">
                        <input
                            v-model="store.search"
                            type="text"
                            placeholder="Buscar por nome"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-[#0042b1] focus:outline-none focus:ring-1 focus:ring-[#0042b1]"
                            @keyup.enter="applySearch"
                        />
                        <button
                            type="button"
                            class="rounded-lg border border-[#0042b1] px-4 py-2 text-sm font-semibold text-[#0042b1] transition hover:bg-blue-50"
                            @click="applySearch"
                        >
                            Buscar
                        </button>
                    </div>

                    <p v-if="store.message" class="mt-4 rounded-lg bg-blue-50 px-4 py-3 text-sm text-[#0042b1]">
                        {{ store.message }}
                    </p>

                    <ul v-if="flattenedErrors.length" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                        <li v-for="(error, index) in flattenedErrors" :key="index">{{ error }}</li>
                    </ul>

                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 text-left text-sm text-slate-700">
                                    <th class="px-4 py-3 font-semibold">Nome</th>
                                    <th class="px-4 py-3 font-semibold">Preço</th>
                                    <th class="px-4 py-3 font-semibold">Estoque</th>
                                    <th class="px-4 py-3 font-semibold">Ações</th>
                                </tr>
                            </thead>
                            <tbody v-if="!store.loading">
                                <tr
                                    v-for="product in store.items"
                                    :key="product.id"
                                    class="border-b border-slate-100 text-sm text-slate-700"
                                >
                                    <td class="px-4 py-3">{{ product.name }}</td>
                                    <td class="px-4 py-3">R$ {{ Number(product.price).toFixed(2) }}</td>
                                    <td class="px-4 py-3">{{ product.stock }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-2">
                                            <button class="rounded border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100" @click="openViewModal(product)">Visualizar</button>
                                            <button class="rounded border border-[#0042b1] px-3 py-1 text-xs font-semibold text-[#0042b1] hover:bg-blue-50" @click="openEditModal(product)">Editar</button>
                                            <button class="rounded border border-red-300 px-3 py-1 text-xs font-semibold text-red-700 hover:bg-red-50" @click="removeProduct(product.id)">Remover</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!store.items.length">
                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">
                                        Nenhum produto encontrado.
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">Carregando produtos...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex flex-col items-start justify-between gap-3 text-sm text-slate-600 md:flex-row md:items-center">
                        <p>Total de registros: {{ store.pagination.total }}</p>
                        <div class="flex items-center gap-2">
                            <button
                                class="rounded border border-slate-300 px-3 py-1 disabled:cursor-not-allowed disabled:opacity-40"
                                :disabled="store.pagination.current_page <= 1"
                                @click="store.fetchProducts(store.pagination.current_page - 1)"
                            >
                                Anterior
                            </button>
                            <span>Página {{ store.pagination.current_page }} de {{ store.pagination.last_page }}</span>
                            <button
                                class="rounded border border-slate-300 px-3 py-1 disabled:cursor-not-allowed disabled:opacity-40"
                                :disabled="store.pagination.current_page >= store.pagination.last_page"
                                @click="store.fetchProducts(store.pagination.current_page + 1)"
                            >
                                Próxima
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-xl rounded-xl bg-white p-6">
                <h3 class="text-lg font-semibold text-[#0042b1]">Novo Produto</h3>
                <div class="mt-4 grid gap-3">
                    <input v-model="createForm.name" type="text" placeholder="Nome" class="rounded-lg border border-slate-300 px-4 py-2 text-sm" />
                    <textarea v-model="createForm.description" rows="3" placeholder="Descrição" class="rounded-lg border border-slate-300 px-4 py-2 text-sm"></textarea>
                    <input v-model="createForm.price" type="number" step="0.01" min="0" placeholder="Preço" class="rounded-lg border border-slate-300 px-4 py-2 text-sm" />
                    <input v-model="createForm.stock" type="number" min="0" placeholder="Estoque" class="rounded-lg border border-slate-300 px-4 py-2 text-sm" />
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button class="rounded border border-slate-300 px-4 py-2 text-sm" @click="showCreateModal = false">Cancelar</button>
                    <button class="rounded bg-[#0042b1] px-4 py-2 text-sm font-semibold text-white disabled:opacity-50" :disabled="store.submitting" @click="createProduct">Salvar</button>
                </div>
            </div>
        </div>

        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-xl rounded-xl bg-white p-6">
                <h3 class="text-lg font-semibold text-[#0042b1]">Editar Produto</h3>
                <div class="mt-4 grid gap-3">
                    <input v-model="editForm.name" type="text" placeholder="Nome" class="rounded-lg border border-slate-300 px-4 py-2 text-sm" />
                    <textarea v-model="editForm.description" rows="3" placeholder="Descrição" class="rounded-lg border border-slate-300 px-4 py-2 text-sm"></textarea>
                    <input v-model="editForm.price" type="number" step="0.01" min="0" placeholder="Preço" class="rounded-lg border border-slate-300 px-4 py-2 text-sm" />
                    <input v-model="editForm.stock" type="number" min="0" placeholder="Estoque" class="rounded-lg border border-slate-300 px-4 py-2 text-sm" />
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button class="rounded border border-slate-300 px-4 py-2 text-sm" @click="showEditModal = false">Cancelar</button>
                    <button class="rounded bg-[#0042b1] px-4 py-2 text-sm font-semibold text-white disabled:opacity-50" :disabled="store.submitting" @click="updateProduct">Atualizar</button>
                </div>
            </div>
        </div>

        <div v-if="showViewModal && store.selectedProduct" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-xl rounded-xl bg-white p-6">
                <h3 class="text-lg font-semibold text-[#0042b1]">Visualizar Produto</h3>
                <dl class="mt-4 space-y-3 text-sm text-slate-700">
                    <div>
                        <dt class="font-semibold">Nome</dt>
                        <dd>{{ store.selectedProduct.name }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Descrição</dt>
                        <dd>{{ store.selectedProduct.description || 'Sem descrição' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Preço</dt>
                        <dd>R$ {{ Number(store.selectedProduct.price).toFixed(2) }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Estoque</dt>
                        <dd>{{ store.selectedProduct.stock }}</dd>
                    </div>
                </dl>
                <div class="mt-6 flex justify-end">
                    <button class="rounded border border-slate-300 px-4 py-2 text-sm" @click="showViewModal = false">Fechar</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
