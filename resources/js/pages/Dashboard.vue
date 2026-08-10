<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Users, Wallet, Package, UserCog } from '@lucide/vue';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

interface Stats {
    customers: number;
    loan_products: number;
    users: number;
    disbursed_loans: number;
}

interface Loan {
    id: number;
    principal: string;
    status: string;
    created_at: string;
    customer: {
        name: string;
    };
    loanProduct: {
        name: string;
    };
}

defineProps<{
    stats: Stats;
    recentLoans: Loan[];
}>();
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">

        <!-- Header -->
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">
                Dashboard
            </h1>

            <p class="text-sm text-muted-foreground">
                Overview of your lending operations.
            </p>
        </div>

        <!-- Statistics -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

            <!-- Customers -->
            <div class="rounded-xl border bg-card p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">
                            Customers
                        </p>

                        <p class="mt-2 text-3xl font-semibold">
                            {{ stats.customers }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-muted p-3">
                        <Users class="h-5 w-5" />
                    </div>
                </div>
            </div>

            <!-- Disbursed Loans -->
            <div class="rounded-xl border bg-card p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">
                            Disbursed Loans
                        </p>

                        <p class="mt-2 text-3xl font-semibold">
                            {{ stats.disbursed_loans }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-muted p-3">
                        <Wallet class="h-5 w-5" />
                    </div>
                </div>
            </div>

            <!-- Loan Products -->
            <div class="rounded-xl border bg-card p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">
                            Active Products
                        </p>

                        <p class="mt-2 text-3xl font-semibold">
                            {{ stats.loan_products }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-muted p-3">
                        <Package class="h-5 w-5" />
                    </div>
                </div>
            </div>

            <!-- Users -->
            <div class="rounded-xl border bg-card p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">
                            System Users
                        </p>

                        <p class="mt-2 text-3xl font-semibold">
                            {{ stats.users }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-muted p-3">
                        <UserCog class="h-5 w-5" />
                    </div>
                </div>
            </div>

        </div>

        <!-- Recent Loans -->
        <div class="rounded-xl border bg-card shadow-sm">

            <div class="border-b p-5">
                <h2 class="font-semibold">
                    Recent Loans
                </h2>

                <p class="text-sm text-muted-foreground">
                    Latest loan applications in the system.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-muted-foreground">
                            <th class="px-5 py-3 font-medium">
                                Loan
                            </th>

                            <th class="px-5 py-3 font-medium">
                                Customer
                            </th>

                            <th class="px-5 py-3 font-medium">
                                Product
                            </th>

                            <th class="px-5 py-3 font-medium">
                                Principal
                            </th>

                            <th class="px-5 py-3 font-medium">
                                Status
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="loan in recentLoans"
                            :key="loan.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-5 py-4 font-medium">
                                #{{ loan.id }}
                            </td>

                            <td class="px-5 py-4">
                                {{ loan.customer.name }}
                            </td>

                            <td class="px-5 py-4">
                                {{ loan.loanProduct.name }}
                            </td>

                            <td class="px-5 py-4">
                                {{ Number(loan.principal).toLocaleString() }}
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="rounded-full bg-muted px-2.5 py-1 text-xs font-medium capitalize"
                                >
                                    {{ loan.status }}
                                </span>
                            </td>
                        </tr>

                        <tr v-if="recentLoans.length === 0">
                            <td
                                colspan="5"
                                class="px-5 py-8 text-center text-muted-foreground"
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
