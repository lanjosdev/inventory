'use client'

import { useState } from 'react'
import Link from 'next/link'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { 
  DropdownMenu, 
  DropdownMenuContent, 
  DropdownMenuItem, 
  DropdownMenuTrigger 
} from '@/components/ui/dropdown-menu'
import { MoreHorizontal, Edit, Trash2, Eye, Building2 } from 'lucide-react'
import type { Company } from '@/types'


interface CompanyListProps {
  companies: Company[]
  onDelete: (company: Company) => void
  isLoading?: boolean
}
export function CompanyList({ companies, onDelete, isLoading }: CompanyListProps) {
  if (isLoading) {
    return (
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {[...Array(6)].map((_, i) => (
          <Card key={i} className="animate-pulse">
            <CardHeader className="space-y-2">
              <div className="h-4 bg-gray-200 rounded w-3/4"></div>
              <div className="h-3 bg-gray-200 rounded w-1/2"></div>
            </CardHeader>
            <CardContent className="space-y-2">
              <div className="h-3 bg-gray-200 rounded w-full"></div>
              <div className="h-3 bg-gray-200 rounded w-2/3"></div>
            </CardContent>
          </Card>
        ))}
      </div>
    )
  }

  if (companies.length === 0) {
    return (
      <Card className="p-8 text-center">
        <div className="mx-auto w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-4">
          <Building2 className="w-6 h-6 text-gray-400" />
        </div>
        <h3 className="text-lg font-semibold mb-2">Nenhuma rede encontrada</h3>
        <p className="text-sm text-gray-500 mb-4">
          Comece adicionando uma nova rede para gerenciar suas lojas e contatos.
        </p>
      </Card>
    )
  }

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      {companies.map((company) => (
        <CompanyCard
          key={company.id}
          company={company}
          onDelete={onDelete}
        />
      ))}
    </div>
  )
}


interface CompanyCardProps {
  company: Company
  onDelete: (company: Company) => void
}
function CompanyCard({ company, onDelete }: CompanyCardProps) {
  const [isMenuOpen, setIsMenuOpen] = useState(false)

  return (
    <Card className="hover:shadow-lg transition-shadow duration-200">
      <CardHeader className="pb-3">
        <div className="flex items-start justify-between">
          <div className="flex-1 min-w-0">
            <CardTitle className="text-lg font-semibold truncate">
              {company.name}
            </CardTitle>
            <p className="text-sm text-gray-500 mt-1">
              {company.contacts.length} contato{company.contacts.length !== 1 ? 's' : ''}
            </p>
          </div>
          <DropdownMenu open={isMenuOpen} onOpenChange={setIsMenuOpen}>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" size="sm" className="h-8 w-8 p-0">
                <MoreHorizontal className="w-4 h-4" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuItem asChild>
                <Link href={`/redes/${company.id}`}>
                  <Eye className="w-4 h-4 mr-2" />
                  Visualizar
                </Link>
              </DropdownMenuItem>
              <DropdownMenuItem asChild>
                <Link href={`/redes/${company.id}/editar`}>
                  <Edit className="w-4 h-4 mr-2" />
                  Editar
                </Link>
              </DropdownMenuItem>
              <DropdownMenuItem 
                onClick={() => onDelete(company)}
                className="text-red-600 hover:text-red-700"
              >
                <Trash2 className="w-4 h-4 mr-2" />
                Excluir
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </CardHeader>
      <CardContent>
        <div className="space-y-3">
          {/* Principais contatos */}
          <div className="space-y-2">
            {company.contacts.slice(0, 2).map((contact) => (
              <div key={contact.id_contact} className="flex items-center gap-2">
                <div className="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium truncate">{contact.name}</p>
                  <p className="text-xs text-gray-500 truncate">{contact.email}</p>
                </div>
              </div>
            ))}
            {company.contacts.length > 2 && (
              <p className="text-xs text-gray-500 pl-4">
                +{company.contacts.length - 2} contato{company.contacts.length - 2 !== 1 ? 's' : ''}
              </p>
            )}
          </div>

          {/* Data de criação */}
          <div className="pt-2 border-t">
            <p className="text-xs text-gray-500">
              Criado em {new Date(company.created_at).toLocaleDateString('pt-BR')}
            </p>
          </div>
        </div>
      </CardContent>
    </Card>
  )
}
