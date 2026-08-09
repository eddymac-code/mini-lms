<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2 } from '@lucide/vue';
import { Button } from '@/components/ui/button';

interface Customer {
    id: number;
    name: string;
    phone: string;
    id_number: string;
    email: string | null;
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Customers',
                href: '/customers',
            },
        ],
    },
});

defineProps<{
    customers: Customer[];
}>();

const deleteCustomer = (customer: Customer) => {
    if (!confirm(`Delete ${customer.name}?`)) {
        return;
    }

    router.delete(`/customers/${customer.id}`);
};
</script>

<template>
    <Head title="Customers" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Customers
                </h1>
                <p class="text-sm text-muted-foreground">
                    Manage borrowers registered in the system.
                </p>
            </div>

            <Button as-child>
                <Link href="/customers/create">
                    <Plus class="mr-2 h-4 w-4" />
                    Add Customer
                </Link>
            </Button>
        </div>

        <div class="overflow-x-auto rounded-xl border">
            <table class="w-full text-sm">
                <thead class="border-b bg-muted/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Name</th>
                        <th class="px-4 py-3 text-left font-medium">Phone</th>
                        <th class="px-4 py-3 text-left font-medium">ID Number</th>
                        <th class="px-4 py-3 text-left font-medium">Email</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="customer in customers"
                        :key="customer.id"
                        class="border-b last:border-0"
                    >
                        <td class="px-4 py-3 font-medium">
                            {{ customer.name }}
                        </td>

                        <td class="px-4 py-3">
                            {{ customer.phone }}
                        </td>

                        <td class="px-4 py-3">
                            {{ customer.id_number }}
                        </td>

                        <td class="px-4 py-3">
                            {{ customer.email ?? '—' }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            <Button
                                variant="ghost"
                                size="icon"
                                as-child
                            >
                                <Link
                                    :href="`/customers/${customer.id}/edit`"
                                >
                                    <Pencil class="h-4 w-4" />
                                </Link>
                            </Button>

                            <Button
                                variant="ghost"
                                size="icon"
                                @click="deleteCustomer(customer)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
