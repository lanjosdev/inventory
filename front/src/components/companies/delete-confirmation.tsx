'use client'

import { useState } from 'react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { AlertTriangle, X } from 'lucide-react'
import type { Company } from '@/types'

interface DeleteConfirmationProps {
  company: Company
  onConfirm: () => Promise<void>
  onCancel: () => void
  isLoading?: boolean
}

export function DeleteConfirmation({ 
  company, 
  onConfirm, 
  onCancel, 
  isLoading = false 
}: DeleteConfirmationProps) {
  const [isDeleting, setIsDeleting] = useState(false)

  const handleConfirm = async () => {
    setIsDeleting(true)
    try {
      await onConfirm()
    } finally {
      setIsDeleting(false)
    }
  }

  return (
    <div 
      className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      style={{ 
        width: '100vw', 
        height: '100vh', 
        top: 0, 
        left: 0, 
        margin: 0, 
        padding: 0 
      }}
    >
      <Card className="w-full max-w-md mx-4">
        <CardHeader className="pb-4">
          <div className="flex items-center justify-between">
            <CardTitle className="flex items-center gap-2 text-red-600">
              <AlertTriangle className="w-5 h-5" />
              Confirmar Exclusão
            </CardTitle>
            <Button
              variant="ghost"
              size="sm"
              onClick={onCancel}
              disabled={isDeleting || isLoading}
            >
              <X className="w-4 h-4" />
            </Button>
          </div>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="text-center">
            <p className="text-lg font-medium">
              Tem certeza que deseja excluir a empresa?
            </p>
            <p className="text-2xl font-bold text-red-600 mt-2">
              {company.name}
            </p>
          </div>
          
          <div className="bg-red-50 border border-red-200 rounded-lg p-4">
            <p className="text-sm text-red-800">
              <strong>Atenção:</strong> Esta ação não pode ser desfeita. 
              Todos os dados relacionados a esta empresa serão removidos permanentemente.
            </p>
            <div className="mt-2 text-sm text-red-700">
              <p>• {company.contacts.length} contato{company.contacts.length !== 1 ? 's' : ''} será{company.contacts.length !== 1 ? 'ão' : ''} removido{company.contacts.length !== 1 ? 's' : ''}</p>
              <p>• Histórico de atividades será perdido</p>
            </div>
          </div>

          <div className="flex gap-3 pt-4">
            <Button
              variant="outline"
              onClick={onCancel}
              disabled={isDeleting || isLoading}
              className="flex-1"
            >
              Cancelar
            </Button>
            <Button
              variant="destructive"
              onClick={handleConfirm}
              disabled={isDeleting || isLoading}
              className="flex-1"
            >
              {isDeleting ? 'Excluindo...' : 'Excluir'}
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
