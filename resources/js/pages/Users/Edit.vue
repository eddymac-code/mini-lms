<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface User {
    id: number;
    name: string;
    email: string;
    password: string;
}

const props = defineProps<{
    user: User;
}>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put(`/users/${props.user.id}`);
};
</script>

<template>
    <Head title="Add User" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div>
            <Button variant="ghost" as-child class="-ml-3 mb-3">
                <Link href="/users">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    System Users
                </Link>
            </Button>

            <h1 class="text-2xl font-semibold tracking-tight">
                Add User
            </h1>

            <p class="text-sm text-muted-foreground">
                Register a new user.
            </p>
        </div>

        <form
            @submit.prevent="submit"
            class="max-w-2xl space-y-6 rounded-xl border p-6"
            autocomplete="false"
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

            <div class="space-y-2">
                <Label for="password">Password</Label>
                <Input
                    id="password"
                    type="password"
                    v-model="form.password"
                    placeholder="Password"
                />
                <p v-if="form.errors.password" class="text-sm text-destructive">
                    {{ form.errors.password }}
                </p>
            </div>

            <div class="space-y-2">
                <Label for="passwordConfirm">Password</Label>
                <Input
                    id="passwordConfirm"
                    type="password"
                    v-model="form.password_confirmation"
                    placeholder="Password"
                />
                <p v-if="form.errors.password_confirmation" class="text-sm text-destructive">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>

            <div class="flex justify-end gap-2">
                <Button variant="outline" as-child>
                    <Link href="/users">
                        Cancel
                    </Link>
                </Button>

                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Updating...' : 'Update User' }}
                </Button>
            </div>
        </form>
    </div>
</template>
