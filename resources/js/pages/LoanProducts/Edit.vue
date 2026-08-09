<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

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
    fee_is_recurring: boolean;
    repayment_order: string;
    is_active: boolean;
}

const props = defineProps<{
    loanProduct: LoanProduct;
}>();

const form = useForm({
    name: props.loanProduct.name,
    min_principal: String(props.loanProduct.min_principal),
    default_principal: String(props.loanProduct.default_principal),
    max_principal: String(props.loanProduct.max_principal),
    interest_rate: String(props.loanProduct.interest_rate),
    interest_method: props.loanProduct.interest_method,
    term: String(props.loanProduct.term),
    term_unit: props.loanProduct.term_unit,
    fees: String(props.loanProduct.fees),
    fee_is_recurring: props.loanProduct.fee_is_recurring,
    repayment_order: props.loanProduct.repayment_order,
    is_active: props.loanProduct.is_active,
});

const submit = () => {
    form.put(`/loan-products/${props.loanProduct.id}`);
};
</script>

<template>
    <Head title="Edit Loan Product" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div>
            <Button
                variant="ghost"
                as-child
                class="-ml-3 mb-3"
            >
                <Link href="/loan-products">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Loan Products
                </Link>
            </Button>

            <h1 class="text-2xl font-semibold tracking-tight">
                Edit Loan Product
            </h1>

            <p class="text-sm text-muted-foreground">
                Update {{ loanProduct.name }}.
            </p>
        </div>

        <form
            @submit.prevent="submit"
            class="max-w-4xl space-y-8 rounded-xl border p-6"
        >
            <section class="space-y-4">
                <div>
                    <h2 class="font-medium">Product Information</h2>
                </div>

                <div class="space-y-2">
                    <Label for="name">Product Name</Label>

                    <Input
                        id="name"
                        v-model="form.name"
                        placeholder="Salary Advance"
                    />

                    <p
                        v-if="form.errors.name"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>
            </section>

            <div class="border-t" />

            <section class="space-y-4">
                <div>
                    <h2 class="font-medium">Principal Limits</h2>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="space-y-2">
                        <Label for="minimum_principal">
                            Minimum Principal
                        </Label>

                        <Input
                            id="min_principal"
                            v-model="form.min_principal"
                            type="number"
                            min="0"
                            step="0.01"
                        />

                        <p
                            v-if="form.errors.min_principal"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.min_principal }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="default_principal">
                            Default Principal
                        </Label>

                        <Input
                            id="default_principal"
                            v-model="form.default_principal"
                            type="number"
                            min="0"
                            step="0.01"
                        />

                        <p
                            v-if="form.errors.default_principal"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.default_principal }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="max_principal">
                            Maximum Principal
                        </Label>

                        <Input
                            id="max_principal"
                            v-model="form.max_principal"
                            type="number"
                            min="0"
                            step="0.01"
                        />

                        <p
                            v-if="form.errors.max_principal"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.max_principal }}
                        </p>
                    </div>
                </div>
            </section>

            <div class="border-t" />

            <section class="space-y-4">
                <div>
                    <h2 class="font-medium">Interest</h2>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="interest_rate">
                            Interest Rate (%)
                        </Label>

                        <Input
                            id="interest_rate"
                            v-model="form.interest_rate"
                            type="number"
                            min="0"
                            step="0.01"
                        />

                        <p
                            v-if="form.errors.interest_rate"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.interest_rate }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="interest_method">
                            Interest Method
                        </Label>

                        <select
                            id="interest_method"
                            v-model="form.interest_method"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="flat">
                                Flat Rate
                            </option>

                            <option value="declining_balance_equal_installment">
                                Reducing Balance - Equal Installments
                            </option>
                        </select>
                    </div>
                </div>
            </section>

            <div class="border-t" />

            <section class="space-y-4">
                <div>
                    <h2 class="font-medium">Repayment Term</h2>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="term">Term</Label>

                        <Input
                            id="term"
                            v-model="form.term"
                            type="number"
                            min="1"
                            step="1"
                        />

                        <p
                            v-if="form.errors.term"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.term }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="term_unit">
                            Term Unit
                        </Label>

                        <select
                            id="term_unit"
                            v-model="form.term_unit"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="weeks">
                                Weeks
                            </option>

                            <option value="months">
                                Months
                            </option>
                        </select>
                    </div>
                </div>
            </section>

            <div class="border-t" />

            <section class="space-y-4">
                <div>
                    <h2 class="font-medium">Fees & Repayment</h2>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="fee">Fee</Label>

                        <Input
                            id="fee"
                            v-model="form.fees"
                            type="number"
                            min="0"
                            step="0.01"
                        />

                        <div class="mt-3 flex items-center gap-2">
                            <input
                                id="fee_is_recurring"
                                v-model="form.fee_is_recurring"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300"
                            />

                            <label
                                for="fee_is_recurring"
                                class="text-sm font-medium"
                            >
                                Recurring fee
                            </label>
                        </div>

                        <p class="mt-1 text-xs text-muted-foreground">
                            If enabled, this fee is charged on every installment.
                            Otherwise, it is charged once on the first installment.
                        </p>

                        <div
                            v-if="form.errors.fee_is_recurring"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.fee_is_recurring }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="repayment_order">
                            Repayment Order
                        </Label>

                        <select
                            id="repayment_order"
                            v-model="form.repayment_order"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="principal_first">
                                Principal First
                            </option>

                            <option value="interest_first">
                                Interest First
                            </option>
                        </select>

                        <p
                            v-if="form.errors.repayment_order"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.repayment_order }}
                        </p>
                    </div>
                </div>
            </section>

            <div class="border-t" />

            <div class="flex items-center gap-3">
                <input
                    id="is_active"
                    v-model="form.is_active"
                    type="checkbox"
                    class="h-4 w-4 rounded border-input"
                />

                <div>
                    <Label for="is_active">
                        Active Product
                    </Label>

                    <p class="text-xs text-muted-foreground">
                        Active products can be used for new loans.
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <Button variant="outline" as-child>
                    <Link href="/loan-products">
                        Cancel
                    </Link>
                </Button>

                <Button
                    type="submit"
                    :disabled="form.processing"
                >
                    {{
                        form.processing
                            ? 'Updating...'
                            : 'Update Loan Product'
                    }}
                </Button>
            </div>
        </form>
    </div>
</template>
