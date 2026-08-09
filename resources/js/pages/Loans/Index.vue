<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Plus, Eye } from '@lucide/vue';
// import { loans } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Loans',
                href: '/loans',
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
    customer: Customer;
    loan_product: LoanProduct;
}

defineProps<{
    loans: Loan[];
}>();

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

function formatStatus(status: string) {
    return status.charAt(0).toUpperCase() + status.slice(1);
}
</script>

<template>
    <Head title="Loans" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Loans
                </h1>

                <p class="text-sm text-muted-foreground">
                    Manage loan applications and disbursements.
                </p>
            </div>

            <Link
                href="/loans/create"
                class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
            >
                <Plus class="h-4 w-4" />
                New Loan
            </Link>
        </div>

        <div class="overflow-hidden rounded-xl border">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">
                                #
                            </th>

                            <th class="px-4 py-3 text-left font-medium">
                                Customer
                            </th>

                            <th class="px-4 py-3 text-left font-medium">
                                Product
                            </th>

                            <th class="px-4 py-3 text-left font-medium">
                                Principal
                            </th>

                            <th class="px-4 py-3 text-left font-medium">
                                Term
                            </th>

                            <th class="px-4 py-3 text-left font-medium">
                                Method
                            </th>

                            <th class="px-4 py-3 text-left font-medium">
                                Status
                            </th>

                            <th class="px-4 py-3 text-right font-medium">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="loan in loans"
                            :key="loan.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-4 py-3">
                                #{{ loan.id }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="font-medium">
                                    {{ loan.customer.name }}
                                </div>

                                <div class="text-xs text-muted-foreground">
                                    {{ loan.customer.phone }}
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                {{ loan.loan_product.name }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                {{ formatMoney(loan.principal) }}
                            </td>

                            <td class="px-4 py-3">
                                {{ loan.term }}
                                {{ loan.term_unit }}
                            </td>

                            <td class="px-4 py-3">
                                {{ formatInterestMethod(loan.interest_method) }}
                            </td>

                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full border px-2.5 py-1 text-xs font-medium"
                                >
                                    {{ formatStatus(loan.status) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <Link
                                    :href="`/loans/${loan.id}`"
                                    class="inline-flex items-center gap-1 rounded-md border px-3 py-1.5 text-sm hover:bg-muted"
                                >
                                    <Eye class="h-4 w-4" />
                                    View
                                </Link>
                            </td>
                        </tr>

                        <tr v-if="loans.length === 0">
                            <td
                                colspan="8"
                                class="px-4 py-10 text-center text-muted-foreground"
                            >
                                No loans have been created yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
