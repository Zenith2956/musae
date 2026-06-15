<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { ref } from 'vue';
import { store } from '@/routes/register';

const roles = [
  { id: 1, name: 'Élève' },
  { id: 2, name: 'Prof' },
];
const selectedRole = ref(0);

</script>

<template>
    <AuthBase
        title="Créer un compte"
        description="Entrez vos informations ci-dessous pour créer votre compte"
    >
        <Head title="Register" />

        <Form
            v-bind="store.form()"
            @submit="store.form().role_id = selectedRole.value"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="nickname">nickname</Label>
                    <Input
                        id="nickname"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="nickname"
                        name="nickname"
                        placeholder="Nickname"
                    />
                    <InputError :message="errors.nickname" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <PasswordInput
                        id="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Password"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <PasswordInput
                        id="password_confirmation"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <div class="grid gap-2">
                    <Label>Rôle</Label>
                    <div class="flex gap-4">
                        <label v-for="role in roles" :key="role.id" class="flex items-center gap-2">
                            <input
                                type="radio"
                                :value="role.id"
                                v-model="selectedRole"
                                name="role_id"
                                required
                            />
                            {{ role.name }}
                        </label>
                    </div>
                    <InputError :message="errors.role_id" />
                </div>

                <Button
                    type="submit"
                    class="btn-log"
                    tabindex="5"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <Spinner v-if="processing" />
                    Créer un compte
                </Button>
            </div>

            <div class="text-center text-sm">
                Déjà un compte ?
                <TextLink
                    :href="login()"
                    class="btn-log"
                    :tabindex="6"
                    >Se connecter</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
