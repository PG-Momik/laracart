<script lang="ts" setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import {Button} from '@/Components/ui/button';
import TextInput from '@/Components/TextInput.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import {ArrowRight, Loader2, Lock, Mail, User} from 'lucide-vue-next';

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
    <Head title="Create Account"/>

    <div class="mb-8">
      <h1 class="text-3xl font-black tracking-tighter text-foreground mb-2">Join Lara Cart</h1>
      <p class="text-muted-foreground font-medium">Create your account to start shopping.</p>
    </div>

    <form class="space-y-5" @submit.prevent="submit">
      <div class="space-y-2">
        <InputLabel class="text-xs font-black uppercase tracking-widest text-muted-foreground ml-1" for="name"
                    value="Full Name"/>
        <div class="relative group">
          <div
              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted-foreground group-focus-within:text-primary transition-colors">
            <User class="size-4"/>
          </div>
          <TextInput
              id="name"
              v-model="form.name"
              autocomplete="name"
              autofocus
              class="block w-full pl-10 h-12 bg-muted/30 border-border/50 focus:bg-background transition-all"
              placeholder="John Doe"
              required
              type="text"
          />
        </div>
        <InputError :message="form.errors.name" class="mt-1"/>
      </div>

      <div class="space-y-2">
        <InputLabel class="text-xs font-black uppercase tracking-widest text-muted-foreground ml-1" for="email"
                    value="Email Address"/>
        <div class="relative group">
          <div
              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted-foreground group-focus-within:text-primary transition-colors">
            <Mail class="size-4"/>
          </div>
          <TextInput
              id="email"
              v-model="form.email"
              autocomplete="username"
              class="block w-full pl-10 h-12 bg-muted/30 border-border/50 focus:bg-background transition-all"
              placeholder="name@example.com"
              required
              type="email"
          />
        </div>
        <InputError :message="form.errors.email" class="mt-1"/>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="space-y-2">
          <InputLabel class="text-xs font-black uppercase tracking-widest text-muted-foreground ml-1" for="password"
                      value="Password"/>
          <div class="relative group">
            <div
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted-foreground group-focus-within:text-primary transition-colors">
              <Lock class="size-4"/>
            </div>
            <TextInput
                id="password"
                v-model="form.password"
                autocomplete="new-password"
                class="block w-full pl-10 h-12 bg-muted/30 border-border/50 focus:bg-background transition-all"
                placeholder="••••••••"
                required
                type="password"
            />
          </div>
          <InputError :message="form.errors.password" class="mt-1"/>
        </div>

        <div class="space-y-2">
          <InputLabel class="text-xs font-black uppercase tracking-widest text-muted-foreground ml-1" for="password_confirmation"
                      value="Confirm"/>
          <div class="relative group">
            <div
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted-foreground group-focus-within:text-primary transition-colors">
              <Lock class="size-4"/>
            </div>
            <TextInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                autocomplete="new-password"
                class="block w-full pl-10 h-12 bg-muted/30 border-border/50 focus:bg-background transition-all"
                placeholder="••••••••"
                required
                type="password"
            />
          </div>
          <InputError :message="form.errors.password_confirmation" class="mt-1"/>
        </div>
      </div>

      <div class="pt-2">
        <Button
            :disabled="form.processing"
            class="w-full h-12 text-base font-black tracking-tight rounded-md bg-primary text-primary-foreground hover:opacity-90 transition-all flex items-center justify-center gap-2 group"
            type="submit"
        >
          <template v-if="form.processing">
            <Loader2 class="size-5 animate-spin"/>
            Creating Account...
          </template>
          <template v-else>
            Create Account
            <ArrowRight class="size-4 group-hover:translate-x-1 transition-transform"/>
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

