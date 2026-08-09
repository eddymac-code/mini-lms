<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle, Send } from '@lucide/vue';
import { loans } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Loans',
                href: loans(),
            },
            {
                title: 'Loan Details',
                href: '#',
            },
        ],
    },
});

interface Customer {
    id: number;
    name: string;
    phone: string;
    id_number?: string;
    email?: string;
}

interface LoanProduct {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
}

interface Installment {
    id: number;
    installment_number: number;
    due_date: string;
    principal_due: string | number;
    interest_due: string | number;
    amount_due: string | number;
    amount_paid: string | number;
    status: string;
    paid_at?: string | null;
}

interface Transaction {
    id: number;
    type: string;
    amount: string | number;
    transaction_date: string;
    reference?: string | null;
}

interface Loan {
    id: number;
    principal: string | number;
    interest_rate: string | number;
    interest_method: string;
    term: number;
    term_unit: string;
    repayment_order: string;
    status: string;
    approved_at?: string | null;
    disbursed_at?: string | null;
    customer: Customer;
    loan_product: LoanProduct;
    approved_by?: User | null;
    disbursed_by?: User | null;
    installments: Installment[];
    transactions: Transaction[];
}

const props = defineProps<{
    loan: Loan;
}>();

const approvalForm = useForm({
    approved_at: '',
});

const disbursementForm = useForm({
    disbursed_at: '',
});

function approve() {
    approvalForm.post(`/loans/${props.loan.id}/approve`);
}

function disburse() {
    disbursementForm.post(`/loans/${props.loan.id}/disburse`);
}

function formatMoney(value: string | number) {
    return Number(value).toLocaleString('en-KE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function formatDate(value?: string | null) {
    if (!value) {
        return '—';
    }

    return new Date(value).toLocaleDateString('en-KE', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function formatDateTime(value?: string | null) {
    if (!value) {
        return '—';
    }

    return new Date(value).toLocaleString('en-KE', {
        dateStyle: 'medium',
        timeStyle: 'short',
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
</script>

<template>
    <Head :title="`Loan #${loan.id}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <Link
                    href="/loans"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-md border hover:bg-muted"
                >
                    <ArrowLeft class="h-4 w-4" />
                </Link>

                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Loan #{{ loan.id }}
                    </h1>

                    <p class="text-sm text-muted-foreground">
                        {{ loan.customer.name }}
                    </p>
                </div>
            </div>

            <span
                class="rounded-full border px-3 py-1 text-sm font-medium capitalize"
            >
                {{ loan.status }}
            </span>
        </div>

        <!-- Actions -->
        <div
            v-if="loan.status === 'pending' || loan.status === 'approved'"
            class="rounded-xl border p-5"
        >
            <h2 class="mb-4 font-semibold">
                Actions
            </h2>

            <div class="flex flex-wrap items-end gap-4">
                <!-- Approval -->
                <form
                    v-if="loan.status === 'pending'"
                    @submit.prevent="approve"
                    class="flex items-end gap-2"
                >
                    <div>
                        <label
                            for="approved_at"
                            class="mb-1 block text-xs font-medium"
                        >
                            Approval Date
                        </label>

                        <input
                            id="approved_at"
                            v-model="approvalForm.approved_at"
                            type="datetime-local"
                            class="h-9 rounded-md border border-input bg-background px-3 text-sm"
                        />
                    </div>

                    <button
                        type="submit"
                        :disabled="approvalForm.processing"
                        class="inline-flex h-9 items-center gap-2 rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground disabled:opacity-50"
                    >
                        <CheckCircle class="h-4 w-4" />

                        {{
                            approvalForm.processing
                                ? 'Approving...'
                                : 'Approve Loan'
                        }}
                    </button>
                </form>

                <!-- Disbursement -->
                <form
                    v-if="loan.status === 'approved'"
                    @submit.prevent="disburse"
                    class="flex items-end gap-2"
                >
                    <div>
                        <label
                            for="disbursed_at"
                            class="mb-1 block text-xs font-medium"
                        >
                            Disbursement Date
                        </label>

                        <input
                            id="disbursed_at"
                            v-model="disbursementForm.disbursed_at"
                            type="datetime-local"
                            class="h-9 rounded-md border border-input bg-background px-3 text-sm"
                        />
                    </div>

                    <button
                        type="submit"
                        :disabled="disbursementForm.processing"
                        class="inline-flex h-9 items-center gap-2 rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground disabled:opacity-50"
                    >
                        <Send class="h-4 w-4" />

                        {{
                            disbursementForm.processing
                                ? 'Disbursing...'
                                : 'Disburse Loan'
                        }}
                    </button>
                </form>
            </div>

            <p
                v-if="approvalForm.errors.loan"
                class="mt-3 text-sm text-destructive"
            >
                {{ approvalForm.errors.loan }}
            </p>

            <p
                v-if="disbursementForm.errors.loan"
                class="mt-3 text-sm text-destructive"
            >
                {{ disbursementForm.errors.loan }}
            </p>
        </div>

        <!-- Loan information -->
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl border p-6">
                <h2 class="mb-5 text-lg font-semibold">
                    Loan Details
                </h2>

                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs text-muted-foreground">
                            Customer
                        </dt>
                        <dd class="font-medium">
                            {{ loan.customer.name }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs text-muted-foreground">
                            Phone
                        </dt>
                        <dd class="font-medium">
                            {{ loan.customer.phone }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs text-muted-foreground">
                            Product
                        </dt>
                        <dd class="font-medium">
                            {{ loan.loan_product.name }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs text-muted-foreground">
                            Principal
                        </dt>
                        <dd class="font-medium">
                            {{ formatMoney(loan.principal) }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs text-muted-foreground">
                            Interest Rate
                        </dt>
                        <dd class="font-medium">
                            {{ loan.interest_rate }}%
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs text-muted-foreground">
                            Interest Method
                        </dt>
                        <dd class="font-medium">
                            {{ formatInterestMethod(loan.interest_method) }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs text-muted-foreground">
                            Term
                        </dt>
                        <dd class="font-medium">
                            {{ loan.term }} {{ loan.term_unit }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs text-muted-foreground">
                            Repayment Order
                        </dt>
                        <dd class="font-medium">
                            {{ formatRepaymentOrder(loan.repayment_order) }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Lifecycle -->
            <div class="rounded-xl border p-6">
                <h2 class="mb-5 text-lg font-semibold">
                    Loan Lifecycle
                </h2>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs text-muted-foreground">
                            Status
                        </dt>
                        <dd class="font-medium capitalize">
                            {{ loan.status }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs text-muted-foreground">
                            Approved By
                        </dt>
                        <dd class="font-medium">
                            {{ loan.approved_by?.name ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs text-muted-foreground">
                            Approved At
                        </dt>
                        <dd class="font-medium">
                            {{ formatDateTime(loan.approved_at) }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs text-muted-foreground">
                            Disbursed By
                        </dt>
                        <dd class="font-medium">
                            {{ loan.disbursed_by?.name ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs text-muted-foreground">
                            Disbursed At
                        </dt>
                        <dd class="font-medium">
                            {{ formatDateTime(loan.disbursed_at) }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Schedule -->
        <div
            v-if="loan.installments.length"
            class="rounded-xl border"
        >
            <div class="border-b p-5">
                <h2 class="text-lg font-semibold">
                    Repayment Schedule
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                #
                            </th>

                            <th class="px-4 py-3 text-left">
                                Due Date
                            </th>

                            <th class="px-4 py-3 text-right">
                                Principal
                            </th>

                            <th class="px-4 py-3 text-right">
                                Interest
                            </th>

                            <th class="px-4 py-3 text-right">
                                Amount Due
                            </th>

                            <th class="px-4 py-3 text-right">
                                Paid
                            </th>

                            <th class="px-4 py-3 text-left">
                                Status
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="installment in loan.installments"
                            :key="installment.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-4 py-3">
                                {{ installment.installment_number }}
                            </td>

                            <td class="px-4 py-3">
                                {{ formatDate(installment.due_date) }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                {{ formatMoney(installment.principal_due) }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                {{ formatMoney(installment.interest_due) }}
                            </td>

                            <td class="px-4 py-3 text-right font-medium">
                                {{ formatMoney(installment.amount_due) }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                {{ formatMoney(installment.amount_paid) }}
                            </td>

                            <td class="px-4 py-3 capitalize">
                                {{ installment.status }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transactions -->
        <div class="rounded-xl border">
            <div class="border-b p-5">
                <h2 class="text-lg font-semibold">
                    Transactions
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                Type
                            </th>

                            <th class="px-4 py-3 text-right">
                                Amount
                            </th>

                            <th class="px-4 py-3 text-left">
                                Date
                            </th>

                            <th class="px-4 py-3 text-left">
                                Reference
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="transaction in loan.transactions"
                            :key="transaction.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-4 py-3 capitalize">
                                {{ transaction.type }}
                            </td>

                            <td class="px-4 py-3 text-right font-medium">
                                {{ formatMoney(transaction.amount) }}
                            </td>

                            <td class="px-4 py-3">
                                {{ formatDateTime(transaction.transaction_date) }}
                            </td>

                            <td class="px-4 py-3">
                                {{ transaction.reference ?? '—' }}
                            </td>
                        </tr>

                        <tr v-if="loan.transactions.length === 0">
                            <td
                                colspan="4"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                No transactions yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
