<script lang="ts" setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import {Button} from '@/Components/ui/button';
import TextInput from '@/Components/TextInput.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import {ArrowRight, Loader2, Lock, Mail} from 'lucide-vue-next';

defineProps<{
  canResetPassword?: boolean;
  status?: string;
}>();

const form = useForm({
  email: 'test.user@laracart.com',
  password: 'password',
  remember: true,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => {
      form.reset('password');
    },
  });
};
</script>

<template>
  <GuestLayout>
    <Head title="Welcome Back"/>

    <div v-if="status" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
      {{ status }}
    </div>

    <div class="mb-8">
      <h1 class="text-3xl font-black tracking-tighter text-foreground mb-2">Welcome back</h1>
      <p class="text-muted-foreground font-medium">Please enter your details to sign in.</p>
    </div>

    <form class="space-y-5" @submit.prevent="submit">
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
              autofocus
              class="block w-full pl-10 h-12 bg-muted/30 border-border/50 focus:bg-background transition-all"
              placeholder="name@example.com"
              required
              type="email"
          />
        </div>
        <InputError :message="form.errors.email" class="mt-1"/>
      </div>

      <div class="space-y-2">
        <div class="flex items-center justify-between ml-1">
          <InputLabel class="text-xs font-black uppercase tracking-widest text-muted-foreground" for="password"
                      value="Password"/>
          <Link
              v-if="canResetPassword"
              :href="route('password.request')"
              class="text-xs font-bold text-primary hover:underline underline-offset-4"
          >
            Forgot?
          </Link>
        </div>
        <div class="relative group">
          <div
              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted-foreground group-focus-within:text-primary transition-colors">
            <Lock class="size-4"/>
          </div>
          <TextInput
              id="password"
              v-model="form.password"
              autocomplete="current-password"
              class="block w-full pl-10 h-12 bg-muted/30 border-border/50 focus:bg-background transition-all"
              placeholder="••••••••"
              required
              type="password"
          />
        </div>
        <InputError :message="form.errors.password" class="mt-1"/>
      </div>

      <div class="flex items-center">
        <label class="flex items-center cursor-pointer group">
          <Checkbox v-model:checked="form.remember" class="size-4" name="remember"/>
          <span
              class="ms-2 text-sm font-bold text-muted-foreground group-hover:text-foreground transition-colors select-none">Keep me signed in</span>
        </label>
      </div>

      <div class="pt-2">
        <Button
            :disabled="form.processing"
            class="w-full h-12 text-base font-black tracking-tight rounded-md bg-primary text-primary-foreground hover:opacity-90 transition-all flex items-center justify-center gap-2 group"
            type="submit"
        >
          <template v-if="form.processing">
            <Loader2 class="size-5 animate-spin"/>
            Signing in...
          </template>
          <template v-else>
            Sign In
            <ArrowRight class="size-4 group-hover:translate-x-1 transition-transform"/>
          </template>
        </Button>
      </div>

      <div class="text-center pt-2">
        <p class="text-sm font-medium text-muted-foreground">
          Don't have an account?
          <Link :href="route('register')" class="text-primary font-black hover:underline underline-offset-4">
            Create one for free
          </Link>
        </p>
      </div>
    </form>
  </GuestLayout>
</template>

