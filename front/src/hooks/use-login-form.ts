import { useState, useTransition } from 'react'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { loginSchema, type LoginFormData } from '@/lib/validations/auth'
import { loginAction } from '@/lib/actions/auth'
import { useRouter } from 'next/navigation'

// TODO: Criar um arquivo central de tipos (ex: src/types/index.ts)
// e mover a interface User para lá.
interface User {
    id: string
    name: string
    email: string
    // Adicione outras propriedades do usuário conforme necessário
}

// Define a interface para a resposta da action, incluindo os erros de campo.
interface ActionResult {
    success: boolean
    message: string
    errors?: Record<string, string[] | undefined>
    user?: User
}

export function useLoginForm() {
    const [isPending, startTransition] = useTransition()
    const router = useRouter()
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

            const result: ActionResult = await loginAction(data);
            console.log('Login result:', result)

            if(result.success) {
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
        message,
        setMessage
    }
}
