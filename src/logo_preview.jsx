import React from 'react';

const LogoPreview = () => {
  return (
    <div className="p-6 bg-gray-50 min-h-screen">
      <h1 className="text-2xl font-bold mb-6 text-gray-800">🎨 Logo Preview - ระบบจองห้องประชุม</h1>
      
      {/* Backend Logo */}
      <div className="bg-white rounded-xl shadow-md p-6 mb-6">
        <h2 className="text-lg font-semibold mb-4 pb-2 border-b border-gray-200">
          Backend Logo (Sidebar - Dark Background)
        </h2>
        <div className="bg-gradient-to-br from-slate-800 to-slate-700 p-5 rounded-lg">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 50" className="h-12">
            <defs>
              <linearGradient id="gradBlue1" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stopColor="#4F46E5"/>
                <stop offset="100%" stopColor="#7C3AED"/>
              </linearGradient>
              <linearGradient id="gradPurple1" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stopColor="#7C3AED"/>
                <stop offset="100%" stopColor="#A855F7"/>
              </linearGradient>
            </defs>
            <g transform="translate(5, 5)">
              <rect x="0" y="6" width="36" height="32" rx="4" fill="url(#gradBlue1)"/>
              <rect x="0" y="6" width="36" height="10" rx="4" fill="url(#gradPurple1)"/>
              <rect x="0" y="12" width="36" height="4" fill="url(#gradPurple1)"/>
              <rect x="8" y="2" width="4" height="8" rx="2" fill="#4F46E5"/>
              <rect x="24" y="2" width="4" height="8" rx="2" fill="#4F46E5"/>
              <circle cx="12" cy="26" r="4" fill="white" opacity="0.9"/>
              <circle cx="24" cy="26" r="4" fill="white" opacity="0.9"/>
              <path d="M8 34 Q12 30 16 34" fill="white" opacity="0.9"/>
              <path d="M20 34 Q24 30 28 34" fill="white" opacity="0.9"/>
            </g>
            <text x="50" y="32" fontFamily="system-ui" fontSize="18" fontWeight="700">
              <tspan fill="#A78BFA">Meeting</tspan><tspan fill="#94A3B8">Room</tspan>
            </text>
            <text x="50" y="44" fontFamily="system-ui" fontSize="9" fontWeight="500" fill="#64748B" letterSpacing="1">
              BACKEND SYSTEM
            </text>
          </svg>
        </div>
      </div>
      
      {/* Frontend Logo */}
      <div className="bg-white rounded-xl shadow-md p-6 mb-6">
        <h2 className="text-lg font-semibold mb-4 pb-2 border-b border-gray-200">
          Frontend Logo (Navbar - Light Background)
        </h2>
        <div className="bg-white border border-gray-200 p-5 rounded-lg">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 220 50" className="h-12">
            <defs>
              <linearGradient id="gradBlue2" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stopColor="#0EA5E9"/>
                <stop offset="100%" stopColor="#06B6D4"/>
              </linearGradient>
              <linearGradient id="gradTeal2" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stopColor="#06B6D4"/>
                <stop offset="100%" stopColor="#14B8A6"/>
              </linearGradient>
            </defs>
            <g transform="translate(5, 5)">
              <rect x="0" y="6" width="36" height="32" rx="4" fill="url(#gradBlue2)"/>
              <rect x="0" y="6" width="36" height="10" rx="4" fill="url(#gradTeal2)"/>
              <rect x="0" y="12" width="36" height="4" fill="url(#gradTeal2)"/>
              <rect x="8" y="2" width="4" height="8" rx="2" fill="#0EA5E9"/>
              <rect x="24" y="2" width="4" height="8" rx="2" fill="#0EA5E9"/>
              <path d="M10 26 L16 32 L28 20" stroke="white" strokeWidth="3" fill="none" strokeLinecap="round" strokeLinejoin="round" opacity="0.95"/>
            </g>
            <text x="50" y="28" fontFamily="system-ui" fontSize="16" fontWeight="700" fill="#0F172A">
              ระบบจองห้องประชุม
            </text>
            <text x="50" y="42" fontFamily="system-ui" fontSize="9" fontWeight="500" fill="#64748B" letterSpacing="0.5">
              Meeting Room Booking
            </text>
          </svg>
        </div>
      </div>
      
      {/* Icon */}
      <div className="bg-white rounded-xl shadow-md p-6">
        <h2 className="text-lg font-semibold mb-4 pb-2 border-b border-gray-200">
          Icon (Favicon)
        </h2>
        <div className="flex gap-8 items-end">
          <div className="text-center">
            <p className="text-sm text-gray-500 mb-2">48×48</p>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48">
              <defs>
                <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stopColor="#4F46E5"/>
                  <stop offset="100%" stopColor="#7C3AED"/>
                </linearGradient>
                <linearGradient id="grad2" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stopColor="#7C3AED"/>
                  <stop offset="100%" stopColor="#A855F7"/>
                </linearGradient>
              </defs>
              <rect x="4" y="10" width="40" height="34" rx="6" fill="url(#grad1)"/>
              <rect x="4" y="10" width="40" height="12" rx="6" fill="url(#grad2)"/>
              <rect x="4" y="18" width="40" height="4" fill="url(#grad2)"/>
              <rect x="14" y="4" width="5" height="10" rx="2.5" fill="#4F46E5"/>
              <rect x="29" y="4" width="5" height="10" rx="2.5" fill="#4F46E5"/>
              <path d="M14 32 L22 40 L36 26" stroke="white" strokeWidth="4" fill="none" strokeLinecap="round" strokeLinejoin="round"/>
            </svg>
          </div>
          <div className="text-center">
            <p className="text-sm text-gray-500 mb-2">32×32</p>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="32" height="32">
              <defs>
                <linearGradient id="grad3" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stopColor="#4F46E5"/>
                  <stop offset="100%" stopColor="#7C3AED"/>
                </linearGradient>
                <linearGradient id="grad4" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stopColor="#7C3AED"/>
                  <stop offset="100%" stopColor="#A855F7"/>
                </linearGradient>
              </defs>
              <rect x="4" y="10" width="40" height="34" rx="6" fill="url(#grad3)"/>
              <rect x="4" y="10" width="40" height="12" rx="6" fill="url(#grad4)"/>
              <rect x="4" y="18" width="40" height="4" fill="url(#grad4)"/>
              <rect x="14" y="4" width="5" height="10" rx="2.5" fill="#4F46E5"/>
              <rect x="29" y="4" width="5" height="10" rx="2.5" fill="#4F46E5"/>
              <path d="M14 32 L22 40 L36 26" stroke="white" strokeWidth="4" fill="none" strokeLinecap="round" strokeLinejoin="round"/>
            </svg>
          </div>
          <div className="text-center">
            <p className="text-sm text-gray-500 mb-2">16×16</p>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="16" height="16">
              <defs>
                <linearGradient id="grad5" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stopColor="#4F46E5"/>
                  <stop offset="100%" stopColor="#7C3AED"/>
                </linearGradient>
                <linearGradient id="grad6" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stopColor="#7C3AED"/>
                  <stop offset="100%" stopColor="#A855F7"/>
                </linearGradient>
              </defs>
              <rect x="4" y="10" width="40" height="34" rx="6" fill="url(#grad5)"/>
              <rect x="4" y="10" width="40" height="12" rx="6" fill="url(#grad6)"/>
              <rect x="4" y="18" width="40" height="4" fill="url(#grad6)"/>
              <rect x="14" y="4" width="5" height="10" rx="2.5" fill="#4F46E5"/>
              <rect x="29" y="4" width="5" height="10" rx="2.5" fill="#4F46E5"/>
              <path d="M14 32 L22 40 L36 26" stroke="white" strokeWidth="4" fill="none" strokeLinecap="round" strokeLinejoin="round"/>
            </svg>
          </div>
        </div>
      </div>
      
      {/* Design Notes */}
      <div className="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <h3 className="font-semibold text-blue-800 mb-2">📝 Design Notes</h3>
        <ul className="text-sm text-blue-700 space-y-1">
          <li>• <strong>Backend:</strong> สีม่วง/น้ำเงิน (Professional, Admin feel)</li>
          <li>• <strong>Frontend:</strong> สีฟ้า/เขียว (Friendly, User-facing)</li>
          <li>• <strong>Icon:</strong> Calendar + Checkmark = จองสำเร็จ</li>
          <li>• <strong>Format:</strong> SVG (Scalable, ความชัดสูง)</li>
        </ul>
      </div>
    </div>
  );
};

export default LogoPreview;
