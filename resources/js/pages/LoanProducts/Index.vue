<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2 } from '@lucide/vue';
import { Button } from '@/components/ui/button';

interface LoanProduct {
    id: number;
    name: string;
    min_principal: number;
    default_principal: number;
    max_principal: number;
    interest_rate: number;
    interest_method: string;
    term: number;
    term_unit: string;
    fees: number;
    repayment_order: string;
    is_active: boolean;
}

defineProps<{
    loanProducts: LoanProduct[];
}>();

const deleteProduct = (product: LoanProduct) => {
    if (
        !confirm(
            `Delete "${product.name}"? This cannot be undone.`,
        )
    ) {
        return;
    }

    router.delete(`/loan-products/${product.id}`);
};

const formatMoney = (value: number) => {
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES',
        maximumFractionDigits: 2,
    }).format(value);
};

const interestMethodLabel = (method: string) => {
    const labels: Record<string, string> = {
        flat: 'Flat Rate',
        declining_balance_equal_installment: 'Reducing Balance',
    };

    return labels[method] ?? method;
};
</script>

<template>
    <Head title="Loan Products" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Loan Products
                </h1>

                <p class="text-sm text-muted-foreground">
                    Manage reusable loan products and their lending terms.
                </p>
            </div>

            <Button as-child>
                <Link href="/loan-products/create">
                    <Plus class="mr-2 h-4 w-4" />
                    Add Loan Product
                </Link>
            </Button>
        </div>

        <div class="overflow-x-auto rounded-xl border">
            <table class="w-full text-sm">
                <thead class="border-b bg-muted/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">
                            Product
                        </th>

                        <th class="px-4 py-3 text-left font-medium">
                            Principal Range
                        </th>

                        <th class="px-4 py-3 text-left font-medium">
                            Interest
                        </th>

                        <th class="px-4 py-3 text-left font-medium">
                            Term
                        </th>

                        <th class="px-4 py-3 text-left font-medium">
                            Fee
                        </th>

                        <th class="px-4 py-3 text-left font-medium">
                            Status
                        </th>

                        <th class="px-4 py-3 text-right font-medium">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-if="loanProducts.length === 0"
                        class="border-b"
                    >
                        <td
                            colspan="7"
                            class="px-4 py-12 text-center text-muted-foreground"
                        >
                            No loan products have been created yet.
                        </td>
                    </tr>

                    <tr
                        v-for="product in loanProducts"
                        :key="product.id"
                        class="border-b last:border-0"
                    >
                        <td class="px-4 py-3">
                            <div class="font-medium">
                                {{ product.name }}
                            </div>

                            <div class="text-xs text-muted-foreground">
                                Default:
                                {{ formatMoney(product.default_principal) }}
                            </div>
                        </td>

                        <td class="px-4 py-3">
                            <div>
                                {{ formatMoney(product.min_principal) }}
                                -
                                {{ formatMoney(product.max_principal) }}
                            </div>
                        </td>

                        <td class="px-4 py-3">
                            <div class="font-medium">
                                {{ product.interest_rate }}%
                            </div>

                            <div class="text-xs text-muted-foreground">
                                {{
                                    interestMethodLabel(
                                        product.interest_method,
                                    )
                                }}
                            </div>
                        </td>

                        <td class="px-4 py-3">
                            {{ product.term }}
                            {{ product.term_unit }}
                        </td>

                        <td class="px-4 py-3">
                            {{ formatMoney(product.fees) }}
                        </td>

                        <td class="px-4 py-3">
                            <span
                                class="rounded-full px-2.5 py-1 text-xs font-medium"
                                :class="
                                    product.is_active
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                        : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
                                "
                            >
                                {{
                                    product.is_active
                                        ? 'Active'
                                        : 'Inactive'
                                }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    as-child
                                >
                                    <Link
                                        :href="`/loan-products/${product.id}/edit`"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                </Button>

                                <Button
                                    variant="ghost"
                                    size="icon"
                                    @click="deleteProduct(product)"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
