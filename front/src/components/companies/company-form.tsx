'use client'

import { useState } from 'react'
import { useForm, useFieldArray } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { Plus, X } from 'lucide-react'
import { createCompanySchema, updateCompanySchema } from '@/lib/validations/company'
import type { CreateCompanyForm, UpdateCompanyForm } from '@/lib/validations/company'
import type { Company } from '@/types'

interface CompanyFormProps {
  company?: Company
  onSubmit: (data: CreateCompanyForm | UpdateCompanyForm) => Promise<void>
  onCancel: () => void
  isLoading?: boolean
}

export function CompanyForm({ company, onSubmit, onCancel, isLoading }: CompanyFormProps) {
  const [isSubmitting, setIsSubmitting] = useState(false)
  const isEditing = !!company

  const schema = isEditing ? updateCompanySchema : createCompanySchema
  
  const {
    register,
    control,
    handleSubmit,
    formState: { errors }
  } = useForm<CreateCompanyForm | UpdateCompanyForm>({
    resolver: zodResolver(schema),
    defaultValues: isEditing ? {
      name: company.name,
      contacts: company.contacts.map(contact => ({
        id_contact: contact.id_contact,
        name: contact.name,
        email: contact.email,
        phone: contact.phone,
        observation: contact.observation || ''
      }))
    } : {
      name: '',
      contacts: [{ name: '', email: '', phone: '', observation: '' }]
    }
  })

  const { fields, append, remove } = useFieldArray({
    control,
    name: 'contacts'
  })

  const handleFormSubmit = async (data: CreateCompanyForm | UpdateCompanyForm) => {
    setIsSubmitting(true)
    try {
      await onSubmit(data)
    } finally {
      setIsSubmitting(false)
    }
  }

  const addContact = () => {
    append({ name: '', email: '', phone: '', observation: '' })
  }

  const removeContact = (index: number) => {
    if (fields.length > 1) {
      remove(index)
    }
  }

  return (
    <Card className="w-full max-w-2xl">
      <CardContent className="pt-6">
        <form onSubmit={handleSubmit(handleFormSubmit)} className="space-y-6">
          {/* Nome da Empresa */}
          <div className="space-y-2">
            <Label htmlFor="name">Nome da Rede</Label>
            <Input
              id="name"
              {...register('name')}
              placeholder="Digite o nome da rede"
              className={errors.name ? 'border-red-500' : ''}
            />
            {errors.name && (
              <p className="text-sm text-red-500">{errors.name.message}</p>
            )}
          </div>

          <Separator />

          {/* Contatos */}
          <div className="space-y-4">
            <div className="flex items-center justify-between">
              <Label className="text-lg font-semibold">Contatos</Label>
              <Button
                type="button"
                variant="outline"
                size="sm"
                onClick={addContact}
                className="flex items-center gap-2"
              >
                <Plus className="w-4 h-4" />
                Adicionar Contato
              </Button>
            </div>

            {fields.map((field, index) => (
              <Card key={field.id} className="p-4">
                <div className="flex items-center justify-between mb-4">
                  <h4 className="font-medium">Contato {index + 1}</h4>
                  {fields.length > 1 && (
                    <Button
                      type="button"
                      variant="ghost"
                      size="sm"
                      onClick={() => removeContact(index)}
                      className="text-red-500 hover:text-red-700"
                    >
                      <X className="w-4 h-4" />
                    </Button>
                  )}
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor={`contacts.${index}.name`}>Nome</Label>
                    <Input
                      id={`contacts.${index}.name`}
                      {...register(`contacts.${index}.name`)}
                      placeholder="Nome do contato"
                      className={errors.contacts?.[index]?.name ? 'border-red-500' : ''}
                    />
                    {errors.contacts?.[index]?.name && (
                      <p className="text-sm text-red-500">
                        {errors.contacts[index]?.name?.message}
                      </p>
                    )}
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor={`contacts.${index}.email`}>E-mail</Label>
                    <Input
                      id={`contacts.${index}.email`}
                      type="email"
                      {...register(`contacts.${index}.email`)}
                      placeholder="email@exemplo.com"
                      className={errors.contacts?.[index]?.email ? 'border-red-500' : ''}
                    />
                    {errors.contacts?.[index]?.email && (
                      <p className="text-sm text-red-500">
                        {errors.contacts[index]?.email?.message}
                      </p>
                    )}
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor={`contacts.${index}.phone`}>Telefone</Label>
                    <Input
                      id={`contacts.${index}.phone`}
                      {...register(`contacts.${index}.phone`)}
                      placeholder="(11) 99999-9999"
                      className={errors.contacts?.[index]?.phone ? 'border-red-500' : ''}
                    />
                    {errors.contacts?.[index]?.phone && (
                      <p className="text-sm text-red-500">
                        {errors.contacts[index]?.phone?.message}
                      </p>
                    )}
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor={`contacts.${index}.observation`}>Observação</Label>
                    <Input
                      id={`contacts.${index}.observation`}
                      {...register(`contacts.${index}.observation`)}
                      placeholder="Observações (opcional)"
                    />
                  </div>
                </div>
              </Card>
            ))}

            {errors.contacts && (
              <p className="text-sm text-red-500">
                {errors.contacts.message}
              </p>
            )}
          </div>

          {/* Botões */}
          <div className="flex gap-4 pt-4">
            <Button
              type="submit"
              disabled={isSubmitting || isLoading}
              className="flex-1"
            >
              {isSubmitting ? 'Salvando...' : isEditing ? 'Atualizar' : 'Cadastrar'}
            </Button>
            <Button
              type="button"
              variant="outline"
              onClick={onCancel}
              disabled={isSubmitting || isLoading}
              className="flex-1"
            >
              Cancelar
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>
  )
}
