<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const form = useForm({
    name: '',
    phone: '',
    id_number: '',
    email: '',
});

const submit = () => {
    form.post('/customers');
};
</script>

<template>
    <Head title="Add Customer" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div>
            <Button variant="ghost" as-child class="-ml-3 mb-3">
                <Link href="/customers">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Customers
                </Link>
            </Button>

            <h1 class="text-2xl font-semibold tracking-tight">
                Add Customer
            </h1>

            <p class="text-sm text-muted-foreground">
                Register a new borrower.
            </p>
        </div>

        <form
            @submit.prevent="submit"
            class="max-w-2xl space-y-6 rounded-xl border p-6"
        >
            <div class="space-y-2">
                <Label for="name">Full Name</Label>
                <Input
                    id="name"
                    v-model="form.name"
                    placeholder="John Doe"
                />
                <p v-if="form.errors.name" class="text-sm text-destructive">
                    {{ form.errors.name }}
                </p>
            </div>

            <div class="space-y-2">
                <Label for="phone">Phone Number</Label>
                <Input
                    id="phone"
                    v-model="form.phone"
                    placeholder="0712345678"
                />
                <p class="text-xs text-muted-foreground">
                    You can enter 0712345678, 254712345678 or +254712345678.
                </p>
                <p v-if="form.errors.phone" class="text-sm text-destructive">
                    {{ form.errors.phone }}
                </p>
            </div>

            <div class="space-y-2">
                <Label for="id_number">ID Number</Label>
                <Input
                    id="id_number"
                    v-model="form.id_number"
                    placeholder="12345678"
                />
                <p
                    v-if="form.errors.id_number"
                    class="text-sm text-destructive"
                >
                    {{ form.errors.id_number }}
                </p>
            </div>

            <div class="space-y-2">
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    type="email"
                    v-model="form.email"
                    placeholder="john@example.com"
                />
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
                    {{ form.processing ? 'Saving...' : 'Save Customer' }}
                </Button>
            </div>
        </form>
    </div>
</template>
