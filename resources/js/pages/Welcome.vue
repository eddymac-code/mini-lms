<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login } from '@/routes';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <!-- <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]"
    >
        <header
            class="mb-6 w-full max-w-83.75 text-sm not-has-[nav]:hidden lg:max-w-4xl"
        >
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                </template>
            </nav>
        </header>
        <div
            class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0"
        >
            <main
                class="flex w-full max-w-83.75 flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row"
            >
                <div
                    class="relative -mb-px aspect-335/364 w-full shrink-0 overflow-hidden rounded-t-lg bg-[#fff2f2] lg:mb-0 lg:-ml-px lg:aspect-auto lg:w-109.5 lg:rounded-t-none lg:rounded-r-lg dark:bg-[#1D0002]"
                >

                    <div
                        class="absolute inset-0 rounded-t-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:rounded-t-none lg:rounded-r-lg dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                    ></div>
                </div>
            </main>
        </div>

    </div> -->
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-background px-6 py-12"
    >
        <div class="w-full max-w-5xl">
            <div
                class="grid items-center gap-12 lg:grid-cols-2"
            >
                <!-- Left -->
                <div class="text-center lg:text-left">
                    <div
                        class="mb-5 inline-flex items-center rounded-full border px-3 py-1 text-sm text-muted-foreground"
                    >
                        Loan Management System
                    </div>

                    <h1
                        class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl"
                    >
                        Simple, reliable
                        <span class="text-primary">loan management.</span>
                    </h1>

                    <p
                        class="mt-6 max-w-xl text-lg leading-8 text-muted-foreground"
                    >
                        Manage customers, loan products, applications,
                        disbursements, repayments and reconciliation from
                        one place.
                    </p>

                    <div
                        class="mt-8 flex flex-col justify-center gap-3 sm:flex-row lg:justify-start"
                    >
                        <Link
                            v-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="login()"
                                class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                            >
                                Log in
                            </Link>
                        </template>
                    </div>
                </div>

                <!-- Right -->
                <div class="grid grid-cols-2 gap-4">
                    <div
                        class="rounded-2xl border bg-card p-6 shadow-sm"
                    >
                        <div
                            class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        >
                            $
                        </div>

                        <h3 class="font-semibold">
                            Loan Management
                        </h3>

                        <p class="mt-2 text-sm text-muted-foreground">
                            Applications, approvals, disbursements and
                            repayment schedules.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border bg-card p-6 shadow-sm"
                    >
                        <div
                            class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        >
                            +
                        </div>

                        <h3 class="font-semibold">
                            Customers
                        </h3>

                        <p class="mt-2 text-sm text-muted-foreground">
                            Keep customer information and loan history
                            organized.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border bg-card p-6 shadow-sm"
                    >
                        <div
                            class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        >
                            ✓
                        </div>

                        <h3 class="font-semibold">
                            Repayments
                        </h3>

                        <p class="mt-2 text-sm text-muted-foreground">
                            Track payments, allocations, fees and
                            outstanding balances.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border bg-card p-6 shadow-sm"
                    >
                        <div
                            class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary"
                        >
                            ↔
                        </div>

                        <h3 class="font-semibold">
                            Reconciliation
                        </h3>

                        <p class="mt-2 text-sm text-muted-foreground">
                            Maintain an auditable record of financial
                            transactions.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div
                id="features"
                class="mt-20 border-t pt-10"
            >
                <div
                    class="grid gap-6 text-center sm:grid-cols-3"
                >
                    <div>
                        <p class="text-2xl font-bold">
                            Customers
                        </p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Centralized customer records
                        </p>
                    </div>

                    <div>
                        <p class="text-2xl font-bold">
                            Loans
                        </p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Complete loan lifecycle management
                        </p>
                    </div>

                    <div>
                        <p class="text-2xl font-bold">
                            Payments
                        </p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Track and reconcile every payment
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
