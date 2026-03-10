interface DashboardCardProps {
  title: string;
  value: string | number;
  icon?: string;
}

const DashboardCard = ({ title, value, icon }: DashboardCardProps) => {
  return (
    <div className="bg-white p-6 rounded-lg shadow-md">
      <div className="flex items-center justify-between">
        <div>
          <h3 className="text-gray-500 text-sm">{title}</h3>
          <p className="text-2xl font-bold">{value}</p>
        </div>
        {icon && <span className="text-3xl">{icon}</span>}
      </div>
    </div>
  );
};

export default DashboardCard;