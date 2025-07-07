'use client'

import { CompanyForm } from '@/components/companies/company-form'
import { createCompanyAction } from '@/lib/actions/companyActions'
import { useToast } from '@/hooks/use-toast'
import { useRouter } from 'next/navigation'
import type { CreateCompanyForm } from '@/lib/validations/company'

export default function CreateNetworkPage() {
  const router = useRouter()
  const { toast } = useToast()

  const handleSubmit = async (data: CreateCompanyForm) => {
    const result = await createCompanyAction(data)
    console.log('Resultado da criação da rede:', result)
    if (result.success) {
      toast.success('Sucesso', result.message || 'Rede criada com sucesso!')
      router.push('/redes')
    } else {
      toast.error('Erro', result.message || 'Ocorreu um erro ao criar a rede.')
    }
  }

  const handleCancel = () => {
    router.push('/redes')
  }

  return (
    <div className="container mx-auto py-10">
      <CompanyForm onSubmit={handleSubmit} onCancel={handleCancel} />
    </div>
  )
}
