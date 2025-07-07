'use client'

import { useState } from 'react'
import Link from 'next/link'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Plus, Search, Building2 } from 'lucide-react'
import { CompanyList } from './company-list'
import { CompanyDetails } from './company-details'
import { DeleteConfirmation } from './delete-confirmation'
import { Pagination } from './pagination'
import { useCompanies } from '@/hooks/use-companies'
import type { Company } from '@/types'

type ViewMode = 'list' | 'edit' | 'details' | 'delete'

export function CompaniesPage() {
  const [viewMode, setViewMode] = useState<ViewMode>('list')
  const [selectedCompany, setSelectedCompany] = useState<Company | null>(null)
  const [searchTerm, setSearchTerm] = useState('')
  
  const {
    companies,
    isLoading,
    currentPage,
    totalPages,
    total,
    deleteCompany,
    setCurrentPage
  } = useCompanies()

  // Filtrar empresas pelo termo de busca
  const filteredCompanies = companies.filter(company =>
    company.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    company.contacts.some(contact => 
      contact.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      contact.email.toLowerCase().includes(searchTerm.toLowerCase())
    )
  )

  const handleEdit = (company: Company) => {
    setSelectedCompany(company)
    setViewMode('edit')
  }

  const handleView = (company: Company) => {
    setSelectedCompany(company)
    setViewMode('details')
  }

  const handleDelete = (company: Company) => {
    setSelectedCompany(company)
    setViewMode('delete')
  }

  const handleBack = () => {
    setSelectedCompany(null)
    setViewMode('list')
  }

  // const handleSubmitUpdate = async (data: UpdateCompanyForm) => {
  //   if (!selectedCompany) return
    
  //   const success = await updateCompany(selectedCompany.id_company, data)
  //   if (success) {
  //     handleBack()
  //   }
  // }

  const handleConfirmDelete = async () => {
    if (!selectedCompany) return
    
    const success = await deleteCompany(selectedCompany.id_company)
    if (success) {
      handleBack()
    }
  }

  // if (viewMode === 'edit' && selectedCompany) {
  //   return (
  //     <div className="container mx-auto p-6">
  //       <CompanyForm
  //         company={selectedCompany}
  //         onSubmit={handleSubmitUpdate}
  //         onCancel={handleBack}
  //         isLoading={isLoading}
  //       />
  //     </div>
  //   )
  // }

  if (viewMode === 'details' && selectedCompany) {
    return (
      <div className="container mx-auto p-6">
        <CompanyDetails
          company={selectedCompany}
          onEdit={handleEdit}
          onBack={handleBack}
        />
      </div>
    )
  }

  return (
    <div className="container mx-auto p-6 space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-3xl font-bold flex items-center gap-2">
            <Building2 className="w-8 h-8 text-blue-600" />
            Redes de Supermercados
          </h1>
          <p className="text-gray-600 mt-1">
            Gerencie suas redes de supermercados e contatos
          </p>
        </div>
        
        <Link href="/redes/cadastrar" passHref>
          <Button className="flex items-center gap-2 w-full sm:w-auto">
            <Plus className="w-4 h-4" />
            Nova Rede
          </Button>
        </Link>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card>
          <CardContent className="p-6">
            <div className="flex items-center gap-2">
              <Building2 className="w-5 h-5 text-blue-600" />
              <div>
                <p className="text-sm text-gray-600">Total de Redes</p>
                <p className="text-2xl font-bold">{total}</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6">
            <div className="flex items-center gap-2">
              <Search className="w-5 h-5 text-green-600" />
              <div>
                <p className="text-sm text-gray-600">Página Atual</p>
                <p className="text-2xl font-bold">{currentPage}</p>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6">
            <div className="flex items-center gap-2">
              <Plus className="w-5 h-5 text-purple-600" />
              <div>
                <p className="text-sm text-gray-600">Total de Páginas</p>
                <p className="text-2xl font-bold">{totalPages}</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Search */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Search className="w-5 h-5" />
            Buscar Empresas
          </CardTitle>
        </CardHeader>

        <CardContent>
          <div className="relative">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
            <Input
              placeholder="Buscar por nome da rede, contato ou e-mail..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="pl-10"
            />
          </div>
        </CardContent>
      </Card>

      {/* Companies List */}
      <CompanyList
        companies={searchTerm ? filteredCompanies : companies}
        onEdit={handleEdit}
        onDelete={handleDelete}
        onView={handleView}
        isLoading={isLoading}
      />

      {/* Pagination */}
      {!searchTerm && (
        <Pagination
          currentPage={currentPage}
          totalPages={totalPages}
          onPageChange={setCurrentPage}
          isLoading={isLoading}
        />
      )}

      {/* Delete Confirmation Modal */}
      {viewMode === 'delete' && selectedCompany && (
        <DeleteConfirmation
          company={selectedCompany}
          onConfirm={handleConfirmDelete}
          onCancel={handleBack}
          isLoading={isLoading}
        />
      )}
    </div>
  )
}
