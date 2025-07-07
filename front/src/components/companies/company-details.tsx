'use client'

import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Separator } from '@/components/ui/separator'
import { Edit, ArrowLeft, Mail, Phone, User, Calendar } from 'lucide-react'
import type { Company } from '@/types'
import Link from 'next/link'

interface CompanyDetailsProps {
  company: Company
  onEdit?: (company: Company) => void
  onBack?: () => void
}

export function CompanyDetails({ company, onEdit, onBack }: CompanyDetailsProps) {
  return (
    <div className="max-w-4xl mx-auto space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          {onBack ? (
            <Button variant="ghost" size="sm" onClick={onBack}>
              <ArrowLeft className="w-4 h-4 mr-2" />
              Voltar
            </Button>
          ) : (
            <Link href="/redes" passHref>
              <Button variant="ghost" size="sm">
                <ArrowLeft className="w-4 h-4 mr-2" />
                Voltar para Redes
              </Button>
            </Link>
          )}
          <div>
            <h1 className="text-2xl font-bold">{company.name}</h1>
            <p className="text-gray-500">
              {company.contacts.length} contato{company.contacts.length !== 1 ? 's' : ''}
            </p>
          </div>
        </div>
        {onEdit ? (
          <Button onClick={() => onEdit(company)}>
            <Edit className="w-4 h-4 mr-2" />
            Editar
          </Button>
        ) : (
          <Link href={`/redes/${company.id}/editar`} passHref>
            <Button>
              <Edit className="w-4 h-4 mr-2" />
              Editar
            </Button>
          </Link>
        )}
      </div>

      {/* Informações Gerais */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <User className="w-5 h-5" />
            Informações Gerais
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label>Nome da Empresa</Label>
              <p className="text-lg font-semibold">{company.name}</p>
            </div>
            <div>
              <Label>ID da Rede</Label>
              <p className="text-lg font-semibold">#{company.id}</p>
            </div>
            <div>
              <Label>Data de Criação</Label>
              <p className="text-lg font-semibold">
                {new Date(company.created_at).toLocaleDateString('pt-BR', {
                  year: 'numeric',
                  month: 'long',
                  day: 'numeric'
                })}
              </p>
            </div>
            <div>
              <Label>Última Atualização</Label>
              <p className="text-lg font-semibold">
                {new Date(company.updated_at).toLocaleDateString('pt-BR', {
                  year: 'numeric',
                  month: 'long',
                  day: 'numeric'
                })}
              </p>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Contatos */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Mail className="w-5 h-5" />
            Contatos ({company.contacts.length})
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {company.contacts.map((contact, index) => (
              <div key={contact.id_contact}>
                <div className="flex items-start justify-between">
                  <div className="flex-1 space-y-2">
                    <div className="flex items-center gap-2">
                      <h4 className="font-semibold">{contact.name}</h4>
                      <Badge variant="secondary">Contato #{contact.id_contact}</Badge>
                    </div>
                    
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div className="flex items-center gap-2">
                        <Mail className="w-4 h-4 text-gray-500" />
                        <span className="text-sm">{contact.email}</span>
                      </div>
                      <div className="flex items-center gap-2">
                        <Phone className="w-4 h-4 text-gray-500" />
                        <span className="text-sm">{contact.phone}</span>
                      </div>
                    </div>
                    
                    {contact.observation && (
                      <div className="mt-2">
                        <Label className="text-sm font-medium text-gray-700">Observação</Label>
                        <p className="text-sm text-gray-600 mt-1">{contact.observation}</p>
                      </div>
                    )}
                  </div>
                </div>
                
                {index < company.contacts.length - 1 && (
                  <Separator className="mt-4" />
                )}
              </div>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Estatísticas */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Calendar className="w-5 h-5" />
            Estatísticas
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className="text-center p-4 bg-blue-50 rounded-lg">
              <div className="text-2xl font-bold text-blue-600">{company.contacts.length}</div>
              <div className="text-sm text-blue-600">Contatos</div>
            </div>
            <div className="text-center p-4 bg-green-50 rounded-lg">
              <div className="text-2xl font-bold text-green-600">
                {company.contacts.filter(c => c.email).length}
              </div>
              <div className="text-sm text-green-600">Com E-mail</div>
            </div>
            <div className="text-center p-4 bg-purple-50 rounded-lg">
              <div className="text-2xl font-bold text-purple-600">
                {company.contacts.filter(c => c.phone).length}
              </div>
              <div className="text-sm text-purple-600">Com Telefone</div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}

function Label({ children, className = '' }: { children: React.ReactNode; className?: string }) {
  return (
    <label className={`text-sm font-medium text-gray-700 ${className}`}>
      {children}
    </label>
  )
}
