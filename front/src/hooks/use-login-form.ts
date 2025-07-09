import { useState, useTransition } from 'react'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { loginSchema, type LoginFormData } from '@/lib/validations/auth'
import { loginAction } from '@/lib/actions/authAction'
import { useRouter } from 'next/navigation'
import type { ActionResult } from '@/types'

export function useLoginForm() {
    const [isPending, startTransition] = useTransition()
    const router = useRouter()
    const [isSuccess, setIsSuccess] = useState(false)
    const [message, setMessage] = useState<{
        type: 'success' | 'error'
        text: string
    } | null>(null)


    const form = useForm<LoginFormData>({
        resolver: zodResolver(loginSchema),
        defaultValues: {
            email: '',
            password: ''
        }
    })

    const onSubmit = (data: LoginFormData) => {
        startTransition(async () => {
            setMessage(null)
            form.clearErrors()
            setIsSuccess(false)

            const result: ActionResult = await loginAction(data);

            if(result.success) {
                setIsSuccess(true)
                setMessage({
                    type: 'success',
                    text: result.message
                })

                // Redireciona para o dashboard após um pequeno delay para o usuário ver a mensagem.
                setTimeout(() => {
                    router.push('/dashboard')
                }, 1000)

            } 
            else {
                // Define a mensagem de erro geral.
                setMessage({
                    type: 'error',
                    text: result.message
                })

                // Se houver erros de campo específicos, define-os no formulário.
                if (result.errors) {
                    Object.entries(result.errors).forEach(([field, errors]) => {
                        if (errors) {
                            form.setError(field as keyof LoginFormData, {
                                type: 'manual',
                                message: errors.join(', ')
                            })
                        }
                    })
                }
            }
        })
    }

    return {
        form,
        onSubmit,
        isPending,
        isSuccess,
        message,
        setMessage
    }
}
