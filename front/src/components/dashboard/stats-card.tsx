import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { cn } from '@/lib/utils';

interface StatsCardProps {
  title: string;
  value: string | number;
  description?: string;
  icon?: React.ReactNode;
  trend?: {
    value: number;
    label: string;
    isPositive: boolean;
  };
  variant?: 'default' | 'networks' | 'stores';
  className?: string;
}

export function StatsCard({
  title,
  value,
  description,
  icon,
  trend,
  variant = 'default',
  className,
}: StatsCardProps) {
  const getVariantClasses = () => {
    switch (variant) {
      case 'networks':
        return 'border-blue-200 bg-blue-50/50 dark:border-blue-800 dark:bg-blue-950/50';
      case 'stores':
        return 'border-green-200 bg-green-50/50 dark:border-green-800 dark:bg-green-950/50';
      default:
        return '';
    }
  };

  const getIconClasses = () => {
    switch (variant) {
      case 'networks':
        return 'text-blue-600 dark:text-blue-400';
      case 'stores':
        return 'text-green-600 dark:text-green-400';
      default:
        return 'text-muted-foreground';
    }
  };

  return (
    <Card className={cn(getVariantClasses(), className)}>
      <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
        <CardTitle className="text-sm font-medium">{title}</CardTitle>
        {icon && <div className={cn('h-4 w-4', getIconClasses())}>{icon}</div>}
      </CardHeader>
      <CardContent>
        <div className="text-2xl font-bold">{value}</div>
        {description && (
          <p className="text-xs text-muted-foreground mt-1">{description}</p>
        )}
        {trend && (
          <div className="flex items-center mt-2">
            <span
              className={cn(
                'text-xs font-medium',
                trend.isPositive ? 'text-green-600' : 'text-red-600'
              )}
            >
              {trend.isPositive ? '+' : '-'}{Math.abs(trend.value)}%
            </span>
            <span className="text-xs text-muted-foreground ml-1">
              {trend.label}
            </span>
          </div>
        )}
      </CardContent>
    </Card>
  );
}
