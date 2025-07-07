'use client'

import { useState, useEffect } from 'react'
import { useToast } from '@/hooks/use-toast'
import { 
  getCompaniesAction, 
  createCompanyAction, 
  updateCompanyAction, 
  deleteCompanyAction 
} from '@/lib/actions/companyActions'
import type { Company } from '@/types'
import type { CreateCompanyForm, UpdateCompanyForm } from '@/lib/validations/company'

interface UseCompaniesReturn {
  companies: Company[]
  isLoading: boolean
  currentPage: number
  totalPages: number
  total: number
  fetchCompanies: (page?: number) => Promise<void>
  createCompany: (data: CreateCompanyForm) => Promise<boolean>
  updateCompany: (id: number, data: UpdateCompanyForm) => Promise<boolean>
  deleteCompany: (id: number) => Promise<boolean>
  setCurrentPage: (page: number) => void
}

export function useCompanies(perPage: number = 10): UseCompaniesReturn {
  const [companies, setCompanies] = useState<Company[]>([])
  const [isLoading, setIsLoading] = useState(true)
  const [currentPage, setCurrentPage] = useState(1)
  const [totalPages, setTotalPages] = useState(1)
  const [total, setTotal] = useState(0)
  const { toast } = useToast()

  const fetchCompanies = async (page: number = currentPage) => {
    setIsLoading(true)
    try {
      const result = await getCompaniesAction(page, perPage)
      
      if (result.success && result.data) {
        setCompanies(result.data.data)
        setCurrentPage(result.data.current_page)
        setTotalPages(result.data.last_page)
        setTotal(result.data.total)
      } else {
        toast.error('Erro', result.message || 'Erro ao carregar empresas')
      }
    } catch {
      toast.error('Erro', 'Erro inesperado ao carregar empresas')
    } finally {
      setIsLoading(false)
    }
  }

  const createCompany = async (data: CreateCompanyForm): Promise<boolean> => {
    try {
      const result = await createCompanyAction(data)
      
      if (result.success) {
        toast.success('Sucesso', result.message || 'Empresa criada com sucesso')
        await fetchCompanies(1) // Recarrega a primeira página
        return true
      } else {
        toast.error('Erro', result.message || 'Erro ao criar empresa')
        return false
      }
    } catch {
      toast.error('Erro', 'Erro inesperado ao criar empresa')
      return false
    }
  }

  const updateCompany = async (id: number, data: UpdateCompanyForm): Promise<boolean> => {
    try {
      const result = await updateCompanyAction(id, data)
      
      if (result.success) {
        toast.success('Sucesso', result.message || 'Empresa atualizada com sucesso')
        await fetchCompanies(currentPage) // Recarrega a página atual
        return true
      } else {
        toast.error('Erro', result.message || 'Erro ao atualizar empresa')
        return false
      }
    } catch {
      toast.error('Erro', 'Erro inesperado ao atualizar empresa')
      return false
    }
  }

  const deleteCompany = async (id: number): Promise<boolean> => {
    try {
      const result = await deleteCompanyAction(id)
      
      if (result.success) {
        toast.success('Sucesso', result.message || 'Empresa excluída com sucesso')
        await fetchCompanies(currentPage) // Recarrega a página atual
        return true
      } else {
        toast.error('Erro', result.message || 'Erro ao excluir empresa')
        return false
      }
    } catch {
      toast.error('Erro', 'Erro inesperado ao excluir empresa')
      return false
    }
  }

  const handleSetCurrentPage = (page: number) => {
    setCurrentPage(page)
    fetchCompanies(page)
  }

  useEffect(() => {
    fetchCompanies(1)
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  return {
    companies,
    isLoading,
    currentPage,
    totalPages,
    total,
    fetchCompanies,
    createCompany,
    updateCompany,
    deleteCompany,
    setCurrentPage: handleSetCurrentPage
  }
}
