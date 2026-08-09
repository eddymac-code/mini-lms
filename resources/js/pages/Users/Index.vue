<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Pencil, Trash2, ShieldCheck } from '@lucide/vue';
import { Button } from '@/components/ui/button';

interface User {
    id: number;
    name: string;
    email: string;
    is_super_admin: boolean;
}

defineProps<{
    users: User[];
}>();

const deleteUser = (user: User) => {
    if (user.is_super_admin) {
        return;
    }

    if (!confirm(`Delete ${user.name}?`)) {
        return;
    }

    router.delete(`/users/${user.id}`);
};
</script>

<template>
    <Head title="System Users" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    System Users
                </h1>

                <p class="text-sm text-muted-foreground">
                    Manage users who operate the loan management system.
                </p>
            </div>

            <Button as-child>
                <Link href="/users/create">
                    <Plus class="mr-2 h-4 w-4" />
                    Add User
                </Link>
            </Button>
        </div>

        <!-- <div class="rounded-xl border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Role</TableHead>
                        <TableHead class="w-[140px] text-right">
                            Actions
                        </TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <TableRow v-if="users.length === 0">
                        <TableCell
                            colspan="4"
                            class="h-24 text-center text-muted-foreground"
                        >
                            No system users found.
                        </TableCell>
                    </TableRow>

                    <TableRow v-for="user in users" :key="user.id">
                        <TableCell class="font-medium">
                            {{ user.name }}
                        </TableCell>

                        <TableCell>
                            {{ user.email }}
                        </TableCell>

                        <TableCell>
                            <div
                                v-if="user.is_super_admin"
                                class="flex items-center gap-2"
                            >
                                <ShieldCheck class="h-4 w-4" />
                                Super Admin
                            </div>

                            <span v-else>
                                System User
                            </span>
                        </TableCell>

                        <TableCell>
                            <div class="flex justify-end gap-1">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    as-child
                                >
                                    <Link :href="`/users/${user.id}/edit`">
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                </Button>

                                <Button
                                    v-if="!user.is_super_admin"
                                    variant="ghost"
                                    size="icon"
                                    @click="deleteUser(user)"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div> -->
        <div class="overflow-x-auto rounded-xl border">
            <table class="w-full text-sm">
                <thead class="border-b bg-muted/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Name</th>
                        <th class="px-4 py-3 text-left font-medium">Email</th>
                        <th class="px-4 py-3 text-left font-medium">Role</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="user in users"
                        :key="user.id"
                        class="border-b last:border-0"
                    >
                        <td class="px-4 py-3 font-medium">
                            {{ user.name }}
                        </td>

                        <td class="px-4 py-3">
                            {{ user.email ?? '—' }}
                        </td>

                        <td class="px-4 py-3">
                            <div
                                v-if="user.is_super_admin"
                                class="flex items-center gap-2"
                            >
                                <ShieldCheck class="h-4 w-4" />
                                Super Admin
                            </div>

                            <span v-else>
                                System User
                            </span>
                        </td>

                        <td class="px-4 py-3 text-right">
                            <Button
                                variant="ghost"
                                size="icon"
                                as-child
                            >
                                <Link :href="`/users/${user.id}/edit`">
                                    <Pencil class="h-4 w-4" />
                                </Link>
                            </Button>

                            <Button
                                v-if="!user.is_super_admin"
                                variant="ghost"
                                size="icon"
                                @click="deleteUser(user)"
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
