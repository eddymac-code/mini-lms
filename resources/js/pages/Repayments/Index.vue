<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Installment {
    id: number;
    installment_number: number;
    due_date: string;
    principal_due: string;
    interest_due: string;
    fee_due: string;
    amount_due: string;
    amount_paid: string;
    status: string;
}

interface Loan {
    id: number;
    principal: string;
    customer: {
        id: number;
        name: string;
        phone: string;
    };
    loan_product: {
        id: number;
        name: string;
    };
    installments: Installment[];
}

const props = defineProps<{
    loans: {
        data: Loan[];
        current_page: number;
        last_page: number;
    };
}>();

const processingLoan = ref<number | null>(null);

function initiatePayment(loan: Loan, installment: Installment) {
    processingLoan.value = installment.id;

    router.post(
        `/loans/${loan.id}/payments`,
        {
            amount:
                Number(installment.amount_due) -
                Number(installment.amount_paid),
        },
        {
            preserveScroll: true,
            onFinish: () => {
                processingLoan.value = null;
            },
        },
    );
}
</script>

<template>
    <Head title="Repayments" />

    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold">
                Repayments
            </h1>

            <p class="text-sm text-muted-foreground">
                Initiate M-Pesa repayments for outstanding loan installments.
            </p>
        </div>

        <div class="space-y-4">
            <div
                v-for="loan in props.loans.data"
                :key="loan.id"
                class="rounded-xl border p-5"
            >
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="font-semibold">
                            Loan #{{ loan.id }} —
                            {{ loan.customer.name }}
                        </h2>

                        <p class="text-sm text-muted-foreground">
                            {{ loan.loan_product.name }}
                            · {{ loan.customer.phone }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left">
                                <th class="p-2">#</th>
                                <th class="p-2">Due Date</th>
                                <th class="p-2">Principal</th>
                                <th class="p-2">Interest</th>
                                <th class="p-2">Fee</th>
                                <th class="p-2">Outstanding</th>
                                <th class="p-2">Status</th>
                                <th class="p-2"></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="installment in loan.installments"
                                :key="installment.id"
                                class="border-b"
                            >
                                <td class="p-2">
                                    {{ installment.installment_number }}
                                </td>

                                <td class="p-2">
                                    {{ installment.due_date }}
                                </td>

                                <td class="p-2">
                                    {{ installment.principal_due }}
                                </td>

                                <td class="p-2">
                                    {{ installment.interest_due }}
                                </td>

                                <td class="p-2">
                                    {{ installment.fee_due }}
                                </td>

                                <td class="p-2 font-medium">
                                    {{
                                        (
                                            Number(installment.amount_due) -
                                            Number(installment.amount_paid)
                                        ).toFixed(2)
                                    }}
                                </td>

                                <td class="p-2 capitalize">
                                    {{ installment.status }}
                                </td>

                                <td class="p-2 text-right">
                                    <button
                                        type="button"
                                        class="rounded-md bg-primary px-3 py-2 text-xs font-medium text-primary-foreground disabled:opacity-50"
                                        :disabled="
                                            processingLoan === installment.id
                                        "
                                        @click="
                                            initiatePayment(
                                                loan,
                                                installment,
                                            )
                                        "
                                    >
                                        {{
                                            processingLoan === installment.id
                                                ? 'Sending...'
                                                : 'Pay via M-Pesa'
                                        }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                v-if="props.loans.data.length === 0"
                class="rounded-xl border p-10 text-center text-muted-foreground"
            >
                No outstanding repayments.
            </div>
        </div>
    </div>
</template>
