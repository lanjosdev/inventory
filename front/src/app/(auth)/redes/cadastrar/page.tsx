'use client'

import { CompanyForm } from '@/components/companies/company-form'
import { createCompanyAction } from '@/lib/actions/companyActions'
import { useToast } from '@/hooks/use-toast'
import { useRouter } from 'next/navigation'
import { Building, ArrowLeft } from 'lucide-react'
import type { CreateCompanyForm } from '@/lib/validations/company'
import { Button } from '@/components/ui/button'

export default function CreateNetworkPage() {
  const router = useRouter()
  const { toast } = useToast()

  const handleSubmit = async (data: CreateCompanyForm) => {
    const result = await createCompanyAction(data)
    
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
    <div className="container mx-auto py-10 px-6 pt-4">
      <div className="mb-8">
        <div className="flex items-center gap-4 mb-4">
          <Button
            variant="ghost"
            size="sm"
            onClick={handleCancel}
            className="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors"
          >
            <ArrowLeft className="w-4 h-4" />
            Voltar
          </Button>
        </div>
        
        <h1 className="text-3xl font-bold flex items-center gap-3 text-gray-900">
          <Building className="w-8 h-8 text-blue-600" />
          Nova Rede
        </h1>

        <p className="text-gray-600 mt-2">
          Cadastre uma nova rede de supermercados no sistema
        </p>
      </div>

      <CompanyForm onSubmit={handleSubmit} onCancel={handleCancel} />
    </div>
  )
}
