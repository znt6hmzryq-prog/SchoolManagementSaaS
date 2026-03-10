import React from 'react';
import { Badge } from './ui/Badge';

interface NotificationDropdownProps {
  notifications: any[];
  onMarkAsRead: (id: number) => void;
  onClose: () => void;
}

export const NotificationDropdown: React.FC<NotificationDropdownProps> = ({
  notifications,
  onMarkAsRead,
  onClose,
}) => {
  return (
    <div className="absolute top-16 right-4 bg-white border shadow-lg p-4 w-80 max-h-96 overflow-y-auto z-50">
      <div className="flex justify-between items-center mb-4">
        <h3 className="text-lg font-semibold">Notifications</h3>
        <button onClick={onClose} className="text-gray-500 hover:text-gray-700">&times;</button>
      </div>
      {notifications.length === 0 ? (
        <p className="text-gray-500">No notifications</p>
      ) : (
        <div className="space-y-2">
          {notifications.map((notification: any) => (
            <div
              key={notification.id}
              className={`p-3 rounded-lg border ${!notification.read_at ? 'bg-blue-50 border-blue-200' : 'bg-gray-50'}`}
              onClick={() => onMarkAsRead(notification.id)}
            >
              <div className="flex justify-between items-start">
                <div>
                  <p className="font-medium">{notification.data?.title}</p>
                  <p className="text-sm text-gray-600">{notification.data?.message}</p>
                  <p className="text-xs text-gray-400 mt-1">{new Date(notification.created_at).toLocaleDateString()}</p>
                </div>
                {!notification.read_at && <Badge variant="info">New</Badge>}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};