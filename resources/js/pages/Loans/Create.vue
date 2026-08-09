<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import { loans } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Loans',
                href: loans(),
            },
            {
                title: 'Create Loan',
                href: '/loans/create',
            },
        ],
    },
});

interface Customer {
    id: number;
    name: string;
    phone: string;
}

interface LoanProduct {
    id: number;
    name: string;
    min_principal: string | number;
    max_principal: string | number;
    default_principal: string | number;
    interest_rate: string | number;
    interest_method: string;
    term: number;
    term_unit: string;
    repayment_order: string;
    fees: string | number;
}

const props = defineProps<{
    customers: Customer[];
    loanProducts: LoanProduct[];
}>();

const form = useForm({
    customer_id: '',
    loan_product_id: '',
    principal: '',
});

function selectedProduct(): LoanProduct | undefined {
    return props.loanProducts.find(
        product => String(product.id) === String(form.loan_product_id),
    );
}

function selectProduct() {
    const product = selectedProduct();

    if (product) {
        form.principal = String(product.default_principal);
    } else {
        form.principal = '';
    }
}

function formatMoney(value: string | number) {
    return Number(value).toLocaleString('en-KE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function formatInterestMethod(method: string) {
    return method === 'flat'
        ? 'Flat Rate'
        : 'Declining Balance — Equal Installment';
}

function formatRepaymentOrder(order: string) {
    return order === 'principal_first'
        ? 'Principal First'
        : 'Interest First';
}

function submit() {
    form.post('/loans');
}
</script>

<template>
    <Head title="Create Loan" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <div class="flex items-center gap-3">
            <Link
                href="/loans"
                class="inline-flex h-9 w-9 items-center justify-center rounded-md border hover:bg-muted"
            >
                <ArrowLeft class="h-4 w-4" />
            </Link>

            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    New Loan
                </h1>

                <p class="text-sm text-muted-foreground">
                    Create a new loan application.
                </p>
            </div>
        </div>

        <form
            @submit.prevent="submit"
            class="max-w-4xl space-y-6"
        >
            <!-- Customer -->
            <div class="rounded-xl border p-6">
                <h2 class="mb-4 text-lg font-semibold">
                    Borrower
                </h2>

                <div>
                    <label
                        for="customer_id"
                        class="mb-2 block text-sm font-medium"
                    >
                        Customer
                    </label>

                    <select
                        id="customer_id"
                        v-model="form.customer_id"
                        class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm"
                    >
                        <option value="">
                            Select customer
                        </option>

                        <option
                            v-for="customer in customers"
                            :key="customer.id"
                            :value="customer.id"
                        >
                            {{ customer.name }} — {{ customer.phone }}
                        </option>
                    </select>

                    <p
                        v-if="form.errors.customer_id"
                        class="mt-1 text-sm text-destructive"
                    >
                        {{ form.errors.customer_id }}
                    </p>
                </div>
            </div>

            <!-- Product -->
            <div class="rounded-xl border p-6">
                <h2 class="mb-4 text-lg font-semibold">
                    Loan Product
                </h2>

                <div class="space-y-5">
                    <div>
                        <label
                            for="loan_product_id"
                            class="mb-2 block text-sm font-medium"
                        >
                            Product
                        </label>

                        <select
                            id="loan_product_id"
                            v-model="form.loan_product_id"
                            @change="selectProduct"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm"
                        >
                            <option value="">
                                Select loan product
                            </option>

                            <option
                                v-for="product in loanProducts"
                                :key="product.id"
                                :value="product.id"
                            >
                                {{ product.name }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.loan_product_id"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ form.errors.loan_product_id }}
                        </p>
                    </div>

                    <div
                        v-if="selectedProduct()"
                        class="grid gap-4 rounded-lg bg-muted/40 p-4 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <div>
                            <p class="text-xs text-muted-foreground">
                                Principal Range
                            </p>

                            <p class="font-medium">
                                {{ formatMoney(selectedProduct()!.min_principal) }}
                                –
                                {{ formatMoney(selectedProduct()!.max_principal) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-muted-foreground">
                                Interest Rate
                            </p>

                            <p class="font-medium">
                                {{ selectedProduct()!.interest_rate }}%
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-muted-foreground">
                                Interest Method
                            </p>

                            <p class="font-medium">
                                {{ formatInterestMethod(selectedProduct()!.interest_method) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-muted-foreground">
                                Term
                            </p>

                            <p class="font-medium">
                                {{ selectedProduct()!.term }}
                                {{ selectedProduct()!.term_unit }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-muted-foreground">
                                Repayment Order
                            </p>

                            <p class="font-medium">
                                {{ formatRepaymentOrder(selectedProduct()!.repayment_order) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-muted-foreground">
                                Fees
                            </p>

                            <p class="font-medium">
                                {{ formatMoney(selectedProduct()!.fees) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Principal -->
            <div class="rounded-xl border p-6">
                <h2 class="mb-4 text-lg font-semibold">
                    Loan Amount
                </h2>

                <div class="max-w-md">
                    <label
                        for="principal"
                        class="mb-2 block text-sm font-medium"
                    >
                        Principal
                    </label>

                    <input
                        id="principal"
                        v-model="form.principal"
                        type="number"
                        min="0"
                        step="0.01"
                        placeholder="Enter principal amount"
                        class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm"
                    />

                    <p
                        v-if="selectedProduct()"
                        class="mt-1 text-xs text-muted-foreground"
                    >
                        Allowed:
                        {{ formatMoney(selectedProduct()!.min_principal) }}
                        –
                        {{ formatMoney(selectedProduct()!.max_principal) }}
                    </p>

                    <p
                        v-if="form.errors.principal"
                        class="mt-1 text-sm text-destructive"
                    >
                        {{ form.errors.principal }}
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <Link
                    href="/loans"
                    class="rounded-md border px-4 py-2 text-sm font-medium hover:bg-muted"
                >
                    Cancel
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ form.processing ? 'Creating...' : 'Create Loan' }}
                </button>
            </div>
        </form>
    </div>
</template>
