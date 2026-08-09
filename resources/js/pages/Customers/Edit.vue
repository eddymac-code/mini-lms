<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface Customer {
    id: number;
    name: string;
    phone: string;
    id_number: string;
    email: string | null;
}

const props = defineProps<{
    customer: Customer;
}>();

const form = useForm({
    name: props.customer.name,
    phone: props.customer.phone,
    id_number: props.customer.id_number,
    email: props.customer.email ?? '',
});

const submit = () => {
    form.put(`/customers/${props.customer.id}`);
};
</script>

<template>
    <Head title="Edit Customer" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div>
            <Button variant="ghost" as-child class="-ml-3 mb-3">
                <Link href="/customers">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Customers
                </Link>
            </Button>

            <h1 class="text-2xl font-semibold tracking-tight">
                Edit Customer
            </h1>

            <p class="text-sm text-muted-foreground">
                Update {{ customer.name }}'s information.
            </p>
        </div>

        <form
            @submit.prevent="submit"
            class="max-w-2xl space-y-6 rounded-xl border p-6"
        >
            <div class="space-y-2">
                <Label for="name">Full Name</Label>
                <Input id="name" v-model="form.name" />
                <p v-if="form.errors.name" class="text-sm text-destructive">
                    {{ form.errors.name }}
                </p>
            </div>

            <div class="space-y-2">
                <Label for="phone">Phone Number</Label>
                <Input id="phone" v-model="form.phone" />
                <p class="text-xs text-muted-foreground">
                    You can enter 0712345678, 254712345678 or +254712345678.
                </p>
                <p v-if="form.errors.phone" class="text-sm text-destructive">
                    {{ form.errors.phone }}
                </p>
            </div>

            <div class="space-y-2">
                <Label for="id_number">ID Number</Label>
                <Input id="id_number" v-model="form.id_number" />
                <p
                    v-if="form.errors.id_number"
                    class="text-sm text-destructive"
                >
                    {{ form.errors.id_number }}
                </p>
            </div>

            <div class="space-y-2">
                <Label for="email">Email</Label>
                <Input id="email" type="email" v-model="form.email" />
                <p v-if="form.errors.email" class="text-sm text-destructive">
                    {{ form.errors.email }}
                </p>
            </div>

            <div class="flex justify-end gap-2">
                <Button variant="outline" as-child>
                    <Link href="/customers">
                        Cancel
                    </Link>
                </Button>

                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Updating...' : 'Update Customer' }}
                </Button>
            </div>
        </form>
    </div>
</template>
