<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Button } from '@/Components/ui/button';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { User, Mail, Lock, Loader2, ArrowRight } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Create Account" />

        <div class="mb-8">
            <h1 class="text-3xl font-black tracking-tighter text-foreground mb-2">Join Lara Cart</h1>
            <p class="text-muted-foreground font-medium">Create your account to start shopping.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div class="space-y-2">
                <InputLabel for="name" value="Full Name" class="text-xs font-black uppercase tracking-widest text-muted-foreground ml-1" />
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted-foreground group-focus-within:text-primary transition-colors">
                        <User class="size-4" />
                    </div>
                    <TextInput
                        id="name"
                        type="text"
                        class="block w-full pl-10 h-12 bg-muted/30 border-border/50 focus:bg-background transition-all"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="John Doe"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.name" />
            </div>

            <div class="space-y-2">
                <InputLabel for="email" value="Email Address" class="text-xs font-black uppercase tracking-widest text-muted-foreground ml-1" />
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted-foreground group-focus-within:text-primary transition-colors">
                        <Mail class="size-4" />
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="block w-full pl-10 h-12 bg-muted/30 border-border/50 focus:bg-background transition-all"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        placeholder="name@example.com"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <InputLabel for="password" value="Password" class="text-xs font-black uppercase tracking-widest text-muted-foreground ml-1" />
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted-foreground group-focus-within:text-primary transition-colors">
                            <Lock class="size-4" />
                        </div>
                        <TextInput
                            id="password"
                            type="password"
                            class="block w-full pl-10 h-12 bg-muted/30 border-border/50 focus:bg-background transition-all"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                        />
                    </div>
                    <InputError class="mt-1" :message="form.errors.password" />
                </div>

                <div class="space-y-2">
                    <InputLabel for="password_confirmation" value="Confirm" class="text-xs font-black uppercase tracking-widest text-muted-foreground ml-1" />
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted-foreground group-focus-within:text-primary transition-colors">
                            <Lock class="size-4" />
                        </div>
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="block w-full pl-10 h-12 bg-muted/30 border-border/50 focus:bg-background transition-all"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                        />
                    </div>
                    <InputError class="mt-1" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div class="pt-2">
                <Button 
                    type="submit"
                    class="w-full h-12 text-base font-black tracking-tight rounded-md bg-primary text-primary-foreground hover:opacity-90 transition-all flex items-center justify-center gap-2 group"
                    :disabled="form.processing"
                >
                    <template v-if="form.processing">
                        <Loader2 class="size-5 animate-spin" />
                        Creating Account...
                    </template>
                    <template v-else>
                        Create Account
                        <ArrowRight class="size-4 group-hover:translate-x-1 transition-transform" />
                    </template>
                </Button>
            </div>
            
            <div class="text-center pt-2">
                <p class="text-sm font-medium text-muted-foreground">
                    Already have an account? 
                    <Link :href="route('login')" class="text-primary font-black hover:underline underline-offset-4">
                        Sign In instead
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>

