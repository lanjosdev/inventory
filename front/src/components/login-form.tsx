"use client"

import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import { Card, CardContent } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { PasswordInput } from "@/components/ui/password-input"
import { FormMessage, FormError } from "@/components/ui/form-message"
import { useLoginForm } from "@/hooks/use-login-form"
import Image from "next/image"
import { Loader2 } from "lucide-react"


export function LoginForm({
  className,
  ...props
}: React.ComponentProps<"div">) {
  const { form, onSubmit, isPending, message } = useLoginForm();

  return (
    <div className={cn("flex flex-col gap-6", className)} {...props}>
      <Card className="overflow-hidden">
        <CardContent className="grid p-0 md:grid-cols-2">
          <form onSubmit={form.handleSubmit(onSubmit)} className="p-6 md:p-8">
            <div className="flex flex-col gap-6">
              <div className="flex flex-col items-center text-center">
                <h1 className="text-2xl font-bold">
                  Bem-vindo
                </h1>
                <p className="text-balance text-muted-foreground">
                  Acesse sua conta da <span className="text-blue-600 font-medium">Plataforma de Mídia Ads</span>
                </p>
              </div>

              {message && (
                <FormMessage type={message.type} message={message.text} />
              )}

              <div className="grid gap-2">
                <Label htmlFor="email">E-mail</Label>
                <Input
                  id="email"
                  type="email"
                  placeholder="seu@email.com"
                  {...form.register('email')}
                  className={cn(
                    form.formState.errors.email && "border-red-500"
                  )}
                />
                <FormError message={form.formState.errors.email?.message} />
              </div>

              <div className="grid gap-2">
                <Label htmlFor="password">Senha</Label>
                <PasswordInput
                  id="password"
                  {...form.register('password')}
                  className={cn(
                    form.formState.errors.password && "border-red-500"
                  )}
                />
                <FormError message={form.formState.errors.password?.message} />
              </div>

              <Button
                type="submit"
                className="w-full"
                disabled={isPending}
              >
                {isPending ? (
                  <>
                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                    Entrando...
                  </>
                ) : (
                  'Entrar'
                )}
              </Button>

            </div>
          </form>
          <div className="relative hidden bg-muted md:block">
            <Image
              src="/placeholder.svg"
              alt="Ilustração da plataforma"
              fill
              className="object-cover dark:brightness-[0.2] dark:grayscale"
            />
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
