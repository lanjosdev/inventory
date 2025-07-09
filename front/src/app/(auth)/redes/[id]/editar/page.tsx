
'use client'

import { CompanyForm, CompanyNotFound } from '@/components/companies'
import { updateCompanyAction, getCompanyByIdAction } from '@/lib/actions/companyActions'
import { useToast } from '@/hooks/use-toast'
import { useRouter } from 'next/navigation'
import { Building } from 'lucide-react'
import { useEffect, useState } from 'react'
import { Card, CardContent } from '@/components/ui/card'
import { Skeleton } from '@/components/ui/skeleton'
import type { UpdateCompanyForm } from '@/lib/validations/company'
import type { Company } from '@/types'

interface EditNetworkPageProps {
  params: { id: string }
}

export default function EditNetworkPage({ params }: EditNetworkPageProps) {
  const router = useRouter()
  const { toast } = useToast()
  const [company, setCompany] = useState<Company | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    const fetchCompany = async () => {
      try {
        setIsLoading(true)
        const companyId = parseInt(params.id)
        
        if (isNaN(companyId)) {
          setError('ID da rede inválido')
          return
        }

        const result = await getCompanyByIdAction(companyId)
        
        if (result.success && result.data) {
          setCompany(result.data)
        } else {
          setError(result.message || 'Rede não encontrada')
        }
      } catch (err) {
        console.error('Erro ao buscar empresa:', err)
        setError('Erro inesperado ao carregar a rede')
      } finally {
        setIsLoading(false)
      }
    }

    fetchCompany()
  }, [params.id])

  const handleSubmit = async (data: UpdateCompanyForm) => {
    try {
      const companyId = parseInt(params.id)
      const result = await updateCompanyAction(companyId, data)
      
      if (result.success) {
        toast.success('Sucesso', result.message || 'Rede atualizada com sucesso!')
        router.push('/redes')
      } else {
        toast.error('Erro', result.message || 'Ocorreu um erro ao atualizar a rede.')
      }
    } catch (err) {
      console.error('Erro ao atualizar empresa:', err)
      toast.error('Erro', 'Erro inesperado ao atualizar a rede.')
    }
  }

  const handleCancel = () => {
    router.push('/redes')
  }

  if (isLoading) {
    return (
      <div className="container mx-auto py-10">
        <div className="mb-8">
          <div className="flex items-center gap-3 mb-2">
            <Building className="w-8 h-8 text-blue-600" />
            <Skeleton className="h-8 w-48" />
          </div>
          <Skeleton className="h-5 w-80" />
        </div>
        <Card className="w-full max-w-2xl mx-auto">
          <CardContent className="pt-6">
            <div className="space-y-6">
              <Skeleton className="h-20 w-full" />
              <Skeleton className="h-40 w-full" />
              <Skeleton className="h-12 w-full" />
            </div>
          </CardContent>
        </Card>
      </div>
    )
  }

  if (error || !company) {
    return <CompanyNotFound companyId={params.id} message={error || undefined} />
  }

  return (
    <div className="container mx-auto py-10">
      <div className="mb-8">
        <h1 className="text-3xl font-bold flex items-center gap-3 text-gray-900">
          <Building className="w-8 h-8 text-blue-600" />
          Editar Rede
        </h1>
        <p className="text-gray-600 mt-2">
          Edite as informações da rede {company.name}
        </p>
      </div>
      <CompanyForm 
        company={company} 
        onSubmit={handleSubmit} 
        onCancel={handleCancel} 
      />
    </div>
  )
}